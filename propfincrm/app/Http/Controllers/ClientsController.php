<?php
namespace App\Http\Controllers;

use Config;
use Dinero;
use Datatables;
use App\Models\Client;
use App\Models\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Repositories\User\UserRepositoryContract;
use App\Repositories\Client\ClientRepositoryContract;
use App\Repositories\Setting\SettingRepositoryContract;

class ClientsController extends Controller
{

    protected $users;
    protected $clients;
    protected $settings;

    public function __construct(
        UserRepositoryContract $users,
        ClientRepositoryContract $clients,
        SettingRepositoryContract $settings
    )
    {
        $this->users = $users;
        $this->clients = $clients;
        $this->settings = $settings;
        $this->middleware('client.create', ['only' => ['create']]);
        $this->middleware('client.update', ['only' => ['edit']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Client::class);
        return view('clients.index');
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {
        $this->authorize('viewAny', Client::class);
        $clients = Client::select(['id', 'name', 'company_name', 'email', 'primary_number']);
        return Datatables::of($clients)
            ->addColumn('namelink', function ($clients) {
                return '<a class="font-semibold text-slate-700 hover:text-orange-600" href="' . route('clients.show', $clients->id) . '">' . e($clients->name) . '</a>';
            })
            ->addColumn('action', function ($client) {
                $items = '';
                if (\Entrust::can('client-update')) {
                    $items .= '<li><a class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50" href="' . route('clients.edit', $client->id) . '"><i class="fa-regular fa-pen-to-square me-2 text-slate-400"></i> Edit</a></li>';
                }
                if (\Entrust::can('client-delete')) {
                    $items .= '<li><hr class="dropdown-divider my-1"></li><li><form method="POST" action="' . route('clients.destroy', $client->id) . '" onsubmit="return confirm(\'Are you sure you want to delete this client?\')">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 w-full text-start border-0 bg-transparent cursor-pointer"><i class="fa-regular fa-trash-can me-2 text-rose-500"></i> Delete</button></form></li>';
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
        $this->authorize('create', Client::class);
        return view('clients.create')
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withIndustries($this->clients->listAllIndustries());
    }

    /**
     * @param StoreClientRequest $request
     * @return mixed
     */
    public function store(StoreClientRequest $request)
    {
        $this->clients->create($request->all());
        return redirect()->route('clients.index');
    }

    /**
     * @param Request $vatRequest
     * @return mixed
     */
    public function cvrapiStart(Request $vatRequest)
    {
        return redirect()->back()
            ->with('data', $this->clients->vat($vatRequest));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return mixed
     */
    public function show($id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('view', $client);
        return view('clients.show')
            ->withClient($client)
            ->withCompanyname($this->settings->getCompanyName())
            ->withInvoices($this->clients->getInvoices($id))
            ->withUsers($this->users->getAllUsersWithDepartments());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return mixed
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $this->authorize('update', $client);
        return view('clients.edit')
            ->withClient($client)
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withIndustries($this->clients->listAllIndustries());
    }

    /**
     * @param $id
     * @param UpdateClientRequest $request
     * @return mixed
     */
    public function update($id, UpdateClientRequest $request)
    {
        $this->authorize('update', Client::findOrFail($id));
        $this->clients->update($id, $request);
        Session()->flash('flash_message', 'Client successfully updated');
        return redirect()->route('clients.index');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $this->authorize('delete', Client::findOrFail($id));
        $this->clients->destroy($id);

        return redirect()->route('clients.index');
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateAssign($id, Request $request)
    {
        $client = Client::findOrFail($id);
        $this->authorize('assign', $client);
        abort_unless($request->user()->belongsToActiveCompany(User::findOrFail($request->input('user_assigned_id'))), 422, 'Assignee must belong to the active company.');
        $this->clients->updateAssign($id, $request);
        Session()->flash('flash_message', 'New user is assigned');
        return redirect()->back();
    }

}
