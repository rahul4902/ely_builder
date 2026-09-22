<?php

namespace App\Http\Controllers;

use Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use App\Models\User;
use App\Models\Task;
use App\Http\Requests;
use App\Models\Client;
use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Repositories\User\UserRepositoryContract;
use App\Repositories\Role\RoleRepositoryContract;
use App\Repositories\Department\DepartmentRepositoryContract;
use App\Repositories\Setting\SettingRepositoryContract;
use App\Repositories\Task\TaskRepositoryContract;
use App\Repositories\Lead\LeadRepositoryContract;
use Illuminate\Support\Facades\Auth;
use Entrust;

class UsersController extends Controller
{
    protected $users;
    protected $roles;
    protected $departments;
    protected $settings;

    protected $tasks;
    protected $leads;

    public function __construct(
        UserRepositoryContract $users,
        RoleRepositoryContract $roles,
        DepartmentRepositoryContract $departments,
        SettingRepositoryContract $settings,
        TaskRepositoryContract $tasks,
        LeadRepositoryContract $leads
    ) {
        $this->users = $users;
        $this->roles = $roles;
        $this->departments = $departments;
        $this->settings = $settings;
        $this->tasks = $tasks;
        $this->leads = $leads;
        $this->middleware('user.create', ['only' => ['create']]);
        $this->middleware('company.admin', ['only' => ['destroy']]);
    }

    public function logoutsession(Request $request)
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        return view('users.index')->withUsers($this->users->getAllUsers());
    }

    public function anyData()
    {
        $this->authorize('viewAny', User::class);
        $companyId = Auth::user()->activeCompany()->id;

        if (Auth::user()->companyRole() === 'team_leader') {
            $users = User::select(['id', 'name', 'email', 'work_number'])
                ->whereHas('companies', fn ($query) => $query->where('companies.id', $companyId)->where('company_user.teamlead_user_id', Auth::id()));
        } else {
            $users = User::select(['id', 'name', 'email', 'work_number'])
                ->whereHas('companies', fn ($query) => $query->where('companies.id', $companyId)->where('company_user.is_active', true));
        }
        if (empty(request()->input('order'))) {
            $users = $users->orderBy('id', 'desc');
        }
        return Datatables::of($users)
            ->addColumn('namelink', function ($users) {
                return '<a class="font-semibold text-slate-700 hover:text-orange-600" href="' . route('users.show', $users->id) . '">' . e($users->name) . '</a>';
            })
            ->addColumn('usertype', function ($users) {
                //change by indrajeet
                if (isset($users->userRole->userRoleDetails)) {
                    return $users->userRole->userRoleDetails->name;
                } else {
                    return "";
                }
            })

            ->addColumn('action', function ($user) {
                $items = '';
                if (Entrust::can('user-update')) {
                    $items .= '<li><a class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50" href="' . route('users.edit', $user->id) . '"><i class="fa-regular fa-pen-to-square me-2 text-slate-400"></i> Edit</a></li>';
                }
                if (Entrust::can('user-delete')) {
                    $items .= '<li><hr class="dropdown-divider my-1"></li><li><button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50" onclick="openModal(' . $user->id . ')"><i class="fa-regular fa-trash-can me-2 text-rose-500"></i> Delete</button></li>';
                }
                if ($items === '') {
                    return '';
                }

                return '<div class="dropdown text-end">
                    <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="fa-solid fa-ellipsis text-sm"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">' . $items . '</ul>
                </div>';
            })->escapeColumns([])
            ->make(true);
    }

    /**
     * Json for Data tables
     * @param $id
     * @return mixed
     */
    public function taskData($id)
    {
        $tasks = Task::select(
            ['id', 'title', 'created_at', 'deadline', 'user_assigned_id', 'client_id', 'status']
        )
            ->where('user_assigned_id', $id);
        return Datatables::of($tasks)
            ->addColumn('titlelink', function ($tasks) {
                return '<a href="' . route('tasks.show', $tasks->id) . '">' . $tasks->title . '</a>';
            })
            ->editColumn('created_at', function ($tasks) {
                return $tasks->created_at ? with(new Carbon($tasks->created_at))
                    ->format('d/m/Y') : '';
            })
            ->editColumn('deadline', function ($tasks) {
                return $tasks->created_at ? with(new Carbon($tasks->created_at))
                    ->format('d/m/Y') : '';
            })
            ->editColumn('status', function ($tasks) {
                return $tasks->status == 1 ? '<span class="label label-success">Open</span>' : '<span class="label label-danger">Closed</span>';
            })
            ->editColumn('client_id', function ($tasks) {
                return $tasks->client->name;
            })->escapeColumns([])
            ->make(true);
    }

    /**
     * Json for Data tables
     * @param $id
     * @return mixed
     */
    public function leadData($id)
    {
        $leads = Lead::select(
            ['id', 'title', 'created_at', 'contact_date', 'user_assigned_id', 'client_id', 'status']
        )
            ->where('user_assigned_id', $id);
        return Datatables::of($leads)
            ->addColumn('titlelink', function ($leads) {
                if($leads->title){
                    return '<a href="' . route('leads.show', $leads->id) . '">' . $leads->title . '</a>';
                }
                return '';
                
            })
            ->editColumn('created_at', function ($leads) {
                return $leads->created_at ? with(new Carbon($leads->created_at))
                    ->format('d/m/Y') : '';
            })
            ->editColumn('contact_date', function ($leads) {
                return $leads->created_at ? with(new Carbon($leads->created_at))
                    ->format('d/m/Y') : '';
            })
            ->editColumn('status', function ($leads) {
                return $leads->status == 1 ? '<span class="label label-success">Open</span>' : '<span class="label label-danger">Closed</span>';
            })
            ->editColumn('client_id', function ($tasks) {
                return $tasks->client->name;
            })->escapeColumns([])
            ->make(true);
    }

    /**
     * Json for Data tables
     * @param $id
     * @return mixed
     */
    public function clientData($id)
    {
        $clients = Client::select(['id', 'name', 'company_name', 'primary_number', 'email'])->where('user_id', $id);
        return Datatables::of($clients)
            ->addColumn('clientlink', function ($clients) {
                return '<a href="' . route('clients.show', $clients->id) . '">' . $clients->name . '</a>';
            })
            ->editColumn('created_at', function ($clients) {
                return $clients->created_at ? with(new Carbon($clients->created_at))
                    ->format('d/m/Y') : '';
            })
            ->editColumn('deadline', function ($clients) {
                return $clients->created_at ? with(new Carbon($clients->created_at))
                    ->format('d/m/Y') : '';
            })->escapeColumns([])
            ->make(true);
    }


    /**
     * @return mixed
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('users.create')
            ->withRoles($this->roles->listAllRoles())
            ->withDepartments($this->departments->listAllDepartments())
            ->withTeamlead($this->users->listAllTeamLead());
    }

    /**
     * @param StoreUserRequest $userRequest
     * @return mixed
     */
    public function store(StoreUserRequest $userRequest)
    {
        $getInsertedId = $this->users->create($userRequest);
        return redirect()->route('users.index');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);
        return view('users.show')
            ->withUser($user)
            ->withCompanyname($this->settings->getCompanyName())
            ->withTaskStatistics($this->tasks->totalOpenAndClosedTasks($id))
            ->withLeadStatistics($this->leads->totalOpenAndClosedLeads($id));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        return view('users.edit')
            ->withUser($user)
            ->withRoles($this->roles->listAllRoles())
            ->withDepartments($this->departments->listAllDepartments())
            ->withTeamlead($this->users->listAllTeamLead());
    }

    /**
     * @param $id
     * @param UpdateUserRequest $request
     * @return mixed
     */
    public function update($id, UpdateUserRequest $request)
    {
        $this->authorize('update', User::findOrFail($id));
        $this->users->update($id, $request);
        Session()->flash('flash_message', 'User successfully updated');
        return redirect()->back();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy(Request $request)
    {
        $this->authorize('delete', User::findOrFail($request->id));
        //dd($request->all(),'indrajeet',$request->id);
        $this->users->destroy($request, $request->id);
        Session()->flash('flash_message', 'User successfully Deleted');
        return redirect()->route('users.index');
    }
    public function destroya(Request $request)
    {
        // dd($request->all(),$request->id,'abc');
        $this->users->destroy($request, $request->id);

        Session()->flash('flash_message', 'User successfully Deleted');
        return redirect()->route('users.index');
    }
}
