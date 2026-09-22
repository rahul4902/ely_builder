<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Scheduler;
use App\Models\User;
use App\ProjectMasterTableModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session as FacadesSession;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Session\Session;
use Yajra\Datatables\Datatables; // ✅ keep this

class SchedulerController extends Controller
{
    public function index()
    {
        $users = $this->assignableUsers()->get();

        $projects = ProjectMasterTableModel::where('status', 1)->selectRaw('TRIM(project_name) as project_name')->orderBy('project_name')->get();
        return view('schedulers.index', compact('users','projects'));
    }

    public function create()
    {
        $users = $this->assignableUsers()->get();
        $projects = ProjectMasterTableModel::where('status', 1)->selectRaw('TRIM(project_name) as project_name')->orderBy('project_name')->get();
        return view('schedulers.create',compact('projects','users'));
        // return view('schedulers.create',compact('projects'))->withUsers($users);
    }

    public function store(Request $request)
    {
 
        $this->validate($request, [
            'from_date'  => 'required|date',
            'to_date'    => 'required|date|after_or_equal:from_date',
            'start_time' => [
                'required',
                'date_format:H:i',
                // 'regex:/^(09|1[0-9]|20):[0-5][0-9]$/',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                // 'regex:/^(09|1[0-9]|20):[0-5][0-9]$/',
                // 'after:start_time',
            ],
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'integer',
            'projects' => 'nullable|array',
            'projects.*' => 'string',
        ], [
            'from_date.required'       => 'The From Date field is required.',
            'from_date.date'           => 'The From Date must be a valid date.',
            'to_date.required'         => 'The To Date field is required.',
            'to_date.date'             => 'The To Date must be a valid date.',
            'to_date.after_or_equal'   => 'The To Date must be after or equal to the From Date.',
            'start_time.required'      => 'The Start Time field is required.',
            'start_time.date_format'   => 'The Start Time must be in the format HH:MM.',
            'start_time.regex'         => 'The Start Time must be between 09:00 and 20:59.',
            'end_time.required'        => 'The End Time field is required.',
            'end_time.date_format'     => 'The End Time must be in the format HH:MM.',
            'end_time.regex'           => 'The End Time must be between 09:00 and 20:59.',
            'user_ids.required'        => 'Please select at least one user.',
            'user_ids.array'           => 'Invalid user selection.',
            'user_ids.min'             => 'Please select at least one user.',
            'user_ids.exists'          => 'One or more selected users are invalid.',
            'projects.required'        => 'Please select at least one project.',
            'projects.exists'          => 'One or more selected project are invalid.',
        ]);
        $this->ensureCompanyUsersAndProjects($request);
        $startDateTime = strtotime($request->from_date . ' ' . $request->start_time);
        $endDateTime   = strtotime($request->to_date . ' ' . $request->end_time);

        if ($startDateTime >= $endDateTime) {
            return back()->withErrors([
                'end_time' => 'End Date & Time must be greater than Start Date & Time'
            ])->withInput();
        }
        Scheduler::create([
            'from_date'  => $request->input('from_date'),
            'to_date'    => $request->input('to_date'),
            'start_time' => $request->input('start_time'),
            'end_time'   => $request->input('end_time'),
            'user_ids'   => json_encode($request->input('user_ids')),
            'projects' => $request->input('projects') ? json_encode($request->input('projects')) : null,
        ]);

        return redirect()->route('scheduler.index')
            ->with('flash_message', 'Scheduler created successfully.');
    }

    public function show($id)
    {
        return redirect()->route('scheduler.edit', $id);
    }

    public function edit($id)
    {
        $scheduler = Scheduler::findOrFail($id);
        $users = $this->assignableUsers()->get();
        $projects = ProjectMasterTableModel::where('status', 1)->selectRaw('TRIM(project_name) as project_name')->get();
        return view('schedulers.edit', compact('scheduler', 'users', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'from_date'  => 'required|date',
            'to_date'    => 'required|date|after_or_equal:from_date',
            'start_time' => [
                'required',
                'date_format:H:i',
                // 'regex:/^(09|1[0-9]|20):[0-5][0-9]$/',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                // 'regex:/^(09|1[0-9]|20):[0-5][0-9]$/',
                // 'after:start_time',
            ],
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer',
            'projects' => 'nullable|array',
            'projects.*' => 'string',
        ], [
            'from_date.required'       => 'The From Date field is required.',
            'from_date.date'           => 'The From Date must be a valid date.',
            'to_date.required'         => 'The To Date field is required.',
            'to_date.date'             => 'The To Date must be a valid date.',
            'to_date.after_or_equal'   => 'The To Date must be after or equal to the From Date.',
            'start_time.required'      => 'The Start Time field is required.',
            'start_time.date_format'   => 'The Start Time must be in the format HH:MM.',
            'start_time.regex'         => 'The Start Time must be between 09:00 and 20:59.',
            'end_time.required'        => 'The End Time field is required.',
            'end_time.date_format'     => 'The End Time must be in the format HH:MM.',
            'end_time.regex'           => 'The End Time must be between 09:00 and 20:59.',
            'user_ids.required'        => 'Please select at least one user.',
            'user_ids.array'           => 'Invalid user selection.',
            'user_ids.min'             => 'Please select at least one user.',
            'user_ids.exists'          => 'One or more selected users are invalid.',
            'projects.required'        => 'Please select at least one project.',
            'projects.exists'          => 'One or more selected project are invalid.',
        ]);
        $this->ensureCompanyUsersAndProjects($request);
        $startDateTime = strtotime($request->from_date . ' ' . $request->start_time);
        $endDateTime   = strtotime($request->to_date . ' ' . $request->end_time);

        if ($startDateTime >= $endDateTime) {
            return back()->withErrors([
                'end_time' => 'End Date & Time must be greater than Start Date & Time'
            ])->withInput();
        }

        $scheduler = Scheduler::findOrFail($id);
        $scheduler->from_date  = $request->input('from_date');
        $scheduler->to_date    = $request->input('to_date');
        $scheduler->start_time = $request->input('start_time');
        $scheduler->end_time   = $request->input('end_time');
        $scheduler->user_ids   = json_encode($request->input('user_ids'));
        $scheduler->projects = $request->input('projects') ? json_encode($request->input('projects')) : null;
        $scheduler->save();

        return redirect()->route('scheduler.index')
            ->with('flash_message', 'Scheduler updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $scheduler = Scheduler::findOrFail($id);
            $scheduler->delete();

            return response()->json([
                'status'  => 200,
                'message' => 'Scheduler deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function schedulerListAjax(Request $request)
    {
        $query = Scheduler::query();

        if ($request->filter_from_date) {
            $query->whereDate('from_date', '>=', $request->filter_from_date);
        }
        if ($request->filter_to_date) {
            $query->whereDate('to_date', '<=', $request->filter_to_date);
        }
        if ($request->filter_user_id) {
            $query->where('user_ids', 'LIKE', '%"' . $request->filter_user_id . '"%');
        }
        if ($request->filter_project) {
            $query->where('projects', 'LIKE', '%' . $request->filter_project . '%');
        }
		$query->orderByDesc('created_at');
		
        $allUsers = $this->assignableUsers()->pluck('name', 'id');

        return Datatables::of($query)
            ->addIndexColumn()

            ->editColumn('from_date', function ($row) {
                return date('d-m-Y', strtotime($row->from_date));
            })
            ->editColumn('to_date', function ($row) {
                return date('d-m-Y', strtotime($row->to_date));
            })
            ->editColumn('start_time', function ($row) {
                return date('h:i A', strtotime($row->start_time));
            })
            ->editColumn('end_time', function ($row) {
                return date('h:i A', strtotime($row->end_time));
            })
            ->editColumn('created_at', function ($row) {
                return date('d-m-Y H:i', strtotime($row->created_at));
            })
            ->addColumn('projects_names', function ($row) {
                $projects = is_array($row->projects)
                    ? $row->projects
                    : json_decode($row->projects, true);

                $names = collect($projects ?? [])
                    ->filter()
                    ->implode(', ');

                return $names;
                // return '<span title="'.$names.'">'.$names.'</span>';
            })
            // ✅ addColumn only for VIRTUAL columns — no DB equivalent
            ->addColumn('user_names', function ($row) use ($allUsers) {
                $ids   = is_array($row->user_ids) ? $row->user_ids : json_decode($row->user_ids, true);
                $names = collect($ids)->map(function ($id) use ($allUsers) {
                    return $allUsers->get($id) ?? 'Unknown';
                })->implode(', ');
                return '<span title="' . $names . '">' . $names . '</span>';
            })
            ->addColumn('total_users', function ($row) {
                $ids = is_array($row->user_ids) ? $row->user_ids : json_decode($row->user_ids, true);
                return count($ids);
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown text-end">
                    <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="fa-solid fa-ellipsis text-sm"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 130px;">
                        <li><a class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50" href="' . route('scheduler.edit', $row->id) . '"><i class="fa-regular fa-pen-to-square me-2 text-slate-400"></i> Edit</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><button type="button" class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 w-full text-start border-0 bg-transparent cursor-pointer deleteScheduler" data-id="' . $row->id . '" data-url="' . route('scheduler.destroy', $row->id) . '"><i class="fa-regular fa-trash-can me-2 text-rose-500"></i> Delete</button></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['user_names', 'action'])
            ->make(true);
    }

    private function assignableUsers()
    {
        $company = auth()->user()->activeCompany();
        abort_unless($company, 403, 'Select an active company.');

        return $company->activeUsers()
            ->wherePivotIn('role', ['company_admin', 'team_leader', 'employee'])
            ->select('users.id', 'users.name')
            ->orderBy('users.name');
    }

    private function ensureCompanyUsersAndProjects(Request $request): void
    {
        $allowedUserIds = $this->assignableUsers()->pluck('users.id')->map(fn ($id) => (int) $id);
        $selectedUserIds = collect($request->input('user_ids', []))->map(fn ($id) => (int) $id);
        if ($selectedUserIds->diff($allowedUserIds)->isNotEmpty()) {
            abort(422, 'Schedulers can only assign active users in the current company.');
        }

        $selectedProjects = collect($request->input('projects', []))->filter();
        $allowedProjects = ProjectMasterTableModel::where('status', 1)
            ->whereIn('project_name', $selectedProjects)
            ->pluck('project_name');
        if ($selectedProjects->diff($allowedProjects)->isNotEmpty()) {
            abort(422, 'Schedulers can only assign projects in the current company.');
        }
    }
}
