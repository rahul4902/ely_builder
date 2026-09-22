<?php

namespace App\Http\Controllers;

use Gate;
use App\Models\Task;
use App\Http\Requests;
use App\Models\Integration;
use Illuminate\Http\Request;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Repositories\Task\TaskRepositoryContract;
use App\Repositories\User\UserRepositoryContract;
use App\Repositories\Client\ClientRepositoryContract;
use App\Repositories\Setting\SettingRepositoryContract;
use App\Repositories\Invoice\InvoiceRepositoryContract;
use Carbon\Carbon;
use Yajra\Datatables\Facades\Datatables;

class TasksController extends Controller
{

    protected $request;
    protected $tasks;
    protected $clients;
    protected $settings;
    protected $users;
    protected $invoices;

    public function __construct(
        TaskRepositoryContract $tasks,
        UserRepositoryContract $users,
        ClientRepositoryContract $clients,
        InvoiceRepositoryContract $invoices,
        SettingRepositoryContract $settings
    ) {
        $this->tasks = $tasks;
        $this->users = $users;
        $this->clients = $clients;
        $this->invoices = $invoices;
        $this->settings = $settings;

        $this->middleware('task.create', ['only' => ['create']]);
        $this->middleware('task.update.status', ['only' => ['updateStatus']]);
        $this->middleware('task.assigned', ['only' => ['updateAssign', 'updateTime']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->authorize('viewAny', Task::class);
        return view('tasks.index');
    }

    public function anyData()
    {
        $this->authorize('viewAny', Task::class);
        $tasks = Task::select(
            ['id', 'title', 'created_at', 'deadline', 'user_assigned_id']
        )->where('status', 1)->get();

        return Datatables::of($tasks)
            ->addColumn('titlelink', function ($task) {
                return '<a class="font-semibold text-slate-700 hover:text-orange-600" href="' . route('tasks.show', $task->id) . '">' . e($task->title) . '</a>';
            })
            ->editColumn('created_at', function ($task) {
                return $task->created_at ? with(new Carbon($task->created_at))->format('d/m/Y') : '';
            })
            ->editColumn('deadline', function ($task) {
                return $task->deadline ? with(new Carbon($task->deadline))->format('d/m/Y') : '';
            })
            ->editColumn('user_assigned_id', function ($task) {
                return optional($task->user)->name ?? '—';
            })
            ->addColumn('action', function ($task) {
                $items = '<li><a class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50" href="' . route('tasks.show', $task->id) . '"><i class="fa-regular fa-eye me-2 text-slate-400"></i> View details</a></li>';

                return '<div class="dropdown text-end">
                    <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="fa-solid fa-ellipsis text-sm"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">' . $items . '</ul>
                </div>';
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        $this->authorize('create', Task::class);
        return view('tasks.create')
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withClients($this->clients->listAllClients());
    }

    /**
     * @param StoreTaskRequest $request
     * @return mixed
     */
    public function store(StoreTaskRequest $request) // uses __contrust request
    {
        $getInsertedId = $this->tasks->create($request);
        return redirect()->route("tasks.show", $getInsertedId);
    }


    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function show(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('view', $task);
        $integrationCheck = Integration::first();

        if ($integrationCheck) {

            $api = Integration::getApi('billing');
            if ($api) {
                $apiConnected = true;
                $invoiceContacts = $api->getContacts();
            } else {
                $apiConnected = false;
                $invoiceContacts = [];
            }
        } else {
            $apiConnected = false;
            $invoiceContacts = [];
        }

        return view('tasks.show')
            ->withTasks($task)
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withContacts($invoiceContacts)
            ->withTasktimes($this->tasks->getTaskTime($id))
            ->withCompanyname($this->settings->getCompanyName())
            ->withApiconnected($apiConnected);
    }


    /**
     * Sees if the Settings from backend allows all to complete taks
     * or only assigned user. if only assigned user:
     * @param $id
     * @param Request $request
     * @return
     * @internal param $ [Auth]  $id Checks Logged in users id
     * @internal param $ [Model] $task->user_assigned_id Checks the id of the user assigned to the task
     * If Auth and user_id allow complete else redirect back if all allowed excute
     * else stmt
     */
    public function updateStatus($id, Request $request)
    {
        $this->authorize('complete', Task::findOrFail($id));
        $this->tasks->updateStatus($id, $request);
        Session()->flash('flash_message', 'Task is completed');
        return redirect()->back();
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateAssign($id, Request $request)
    {
        $task = Task::findOrFail($id);
        $this->authorize('assign', $task);
        abort_unless($request->user()->belongsToActiveCompany(\App\Models\User::findOrFail($request->input('user_assigned_id'))), 422, 'Assignee must belong to the active company.');
        $clientId = $this->tasks->getAssignedClient($id)->id;


        $this->tasks->updateAssign($id, $request);
        Session()->flash('flash_message', 'New user is assigned');
        return back();
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateTime($id, Request $request)
    {
        $this->authorize('update', Task::findOrFail($id));
        $this->tasks->updateTime($id, $request);
        Session()->flash('flash_message', 'Time has been updated');
        return redirect()->back();
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function invoice($id, Request $request)
    {
        $task = Task::findOrFail($id);
        $this->authorize('update', $task);
        $clientId = $task->client()->first()->id;
        $timeTaskId = $task->time()->get();
        $integrationCheck = Integration::first();

        if ($integrationCheck) {
            $this->tasks->invoice($id, $request);
        }
        $this->invoices->create($clientId, $timeTaskId, $request->all());
        Session()->flash('flash_message', 'Invoice created');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @return mixed
     * @internal param int $id
     */
    public function marked()
    {
        Notifynder::readAll(auth()->user()->id);
        return back();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        return redirect()->route('tasks.show', $id);
    }
}
