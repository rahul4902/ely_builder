<?php
namespace App\Http\Controllers;

use Session;
use App\Http\Requests;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Repositories\Department\DepartmentRepositoryContract;

class DepartmentsController extends Controller
{

    protected $departments;

    /**
     * DepartmentsController constructor.
     * @param DepartmentRepositoryContract $departments
     */
    public function __construct(DepartmentRepositoryContract $departments)
    {
        $this->departments = $departments;
        $this->middleware('user.is.admin', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('departments.index')
            ->withDepartment($this->departments->getAllDepartments());
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * @param StoreDepartmentRequest $request
     * @return mixed
     */
    public function store(StoreDepartmentRequest $request)
    {
        $this->departments->create($request);
        Session::flash('flash_message', 'Successfully created New Department');
        return redirect()->route('departments.index');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return redirect()->route('departments.edit', $id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $department = $this->departments->find($id);
        return view('departments.edit', compact('department'));
    }

    /**
     * @param $id
     * @param StoreDepartmentRequest $request
     * @return mixed
     */
    public function update($id, StoreDepartmentRequest $request)
    {
        $this->departments->update($id, $request);
        Session::flash('flash_message', 'Successfully updated Department');
        return redirect()->route('departments.index');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $this->departments->destroy($id);
        return redirect()->route('departments.index');
    }
}
