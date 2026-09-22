<?php

namespace App\Http\Controllers;

use App\LeadsFollowUpTableModel;
use App\SellLeadTableModel;
use App\Models\User;

use Carbon\Carbon;
use Faker\Provider\DateTime;
use Yajra\Datatables\Datatables;
use App\LeadModel;
use App\Models\Lead;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\Lead\StoreLeadRequest;
use App\Repositories\Lead\LeadRepositoryContract;
use App\Repositories\Lead\SellLeadRepositoryContract;
use Illuminate\Support\Facades\Storage;
use App\Repositories\User\UserRepositoryContract;
use App\Http\Requests\Lead\UpdateLeadFollowUpRequest;
use App\Models\DumpLead;
use App\Models\Scheduler;
use App\Repositories\Client\ClientRepositoryContract;
use App\Repositories\Setting\SettingRepositoryContract;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Illuminate\Support\Facades\Input;
use FCM;
use App\Services\FCMService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class LeadsController extends Controller
{
    protected $leads;
    //    protected $sell_lead;
    protected $clients;
    protected $settings;
    protected $users;

    public function __construct(
        //        SellLeadRepositoryContract $sell_lead,
        LeadRepositoryContract $leads,
        UserRepositoryContract $users,
        ClientRepositoryContract $clients,
        SettingRepositoryContract $settings
    ) {
        $this->users = $users;
        $this->settings = $settings;
        $this->clients = $clients;
        $this->leads = $leads;
        $this->middleware('lead.create', ['only' => ['create']]);
        $this->middleware('lead.assigned', ['only' => ['updateAssign']]);
        $this->middleware('lead.update.status', ['only' => ['updateStatus']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type = null)
    {
        $users = User::All();
        $followup = [];
        return view('leads.index', compact('type',  'users', 'followup'))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withCompanyname($this->settings->getCompanyName());
    }

    public function exportDumpLeadsToCSV(Request $request)
    {
        $query = DumpLead::leftJoin('users', 'users.id', 'dump_leads1.user_assigned_id')
                ->leftJoin('lead_follow_up', 'lead_follow_up.lead_id', 'dump_leads1.id')
                ->where('dump_leads1.status', 5)
                ->select('dump_leads1.*', 'users.name as user_name')
                ->distinct();
                
        if ($request->has('Platform')) {
            $userType = User::where('user_token', $request->input('key'))->first()->userRole->userRoleDetails->name;
            if ($userType != 'administrator' && $userType != 'employee') {
                $user_id = '';
                $user_id = User::where('teamlead', $user_id)->get()->pluck('id')->toArray();
            } else {
                $user_id = User::where('user_token', $request->input('key'))->first()->id;
            }
        } else {
            $userType = Auth::user()->userRole?->userRoleDetails?->name ?? 'employee';
            if ($userType != 'administrator'  && $userType != 'employee') {
                $user_id = User::where('teamlead', Auth::user()->id)->get()->pluck('id')->toArray();
            } else {
                $user_id = Auth::user()->id;
            }
        }
        
        if ($userType == 'employee') {
            $query = $query->where('dump_leads1.user_assigned_id', $user_id);
        } elseif ($userType != 'administrator') {
            $query = $query->whereIn('dump_leads1.user_assigned_id', (array)$user_id);
        }

        if ($request->filled('date') && $request->filled('end_date')) {
            $query = $query->where('dump_leads1.updated_at', '>=', Carbon::parse($request->input('date'))->startOfDay())
                ->where('dump_leads1.updated_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
        } elseif ($request->filled('date')) {
            $query = $query->where('dump_leads1.updated_at', '>=', Carbon::parse($request->input('date'))->startOfDay());
        } elseif ($request->filled('end_date')) {
            $query = $query->where('dump_leads1.updated_at', '<=', Carbon::parse($request->input('end_date'))->endOfDay());
        }

        if ($request->filled('asign_id') && $request->input('asign_id') != "ALL") {
            $query = $query->where('dump_leads1.user_assigned_id', $request->input('asign_id'));
        }
        
        $leads = $query->orderBy('dump_leads1.created_at','DESC')->get();
        //////        
                
        // Define the columns for export
       
        
        $columns = [
            'Name' => 'Name',
            'Created Date' => 'Created Date',
            'Contact No' => 'Contact No',
            'Next Follow-Up' => 'Next Follow-Up',
            'Last Comment' => 'Last Comment',
            'Location' => 'Location',
            'Project' => 'Project', 
            'Source' => 'Source',
            'Requirement' => 'Requirement',
            'User Name' => 'User Name',
            'User Assign Date' => 'User Assign Date',
            'Team Leader' => 'Team Leader',
            'Meeting Date' => 'Meeting Date',
            'Status' => 'Status',
            'Lead Type' => 'Lead Type',
            'Action Date' => 'Action Date'
        ];

        // Optionally allow dynamic column input
        if ($request->has('columns')) {
            $columns = array_intersect_key(
                $columns,
                array_flip($request->input('columns'))
            );
        }


        // Generate CSV headers
        $csvHeader = array_keys($columns);

        // Prepare the CSV callback
        $callback = function () use ($leads, $csvHeader, $columns) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, $csvHeader);

            // Add data rows
            foreach ($leads as $lead) {
                $follow1meet = null;
                if ($lead->getfollowup != null && !empty($lead->getfollowup)) {
                    $follow1 = $lead->getfollowup->first();
                    $follow1meet = $lead->getfollowup->where('meeting_date', '!=', null)->first();
                }
                $row = [];
                $row[] = $lead->name;
                $row[] = $lead->created_at;
                $row[] = $lead->contact_no;
                $followUpDate = $lead->getfollowup()->select('follow_up_date')->first();
                $date = $followUpDate ? $followUpDate->follow_up_date : '';
                $row[] = strtotime($date) && $date != '0000-00-00 00:00:00' ? date('d-m-Y H:i:s', strtotime($date)) : '';
                $followUpDate = $lead->getfollowup()->select('comment')->first();
                $row[] =  ($followUpDate ? $followUpDate->comment : '');
                $row[] = $lead->location;
                $row[] = $lead->project;
                $row[] = $lead->source;
                $row[] = $lead->requirement;
                $row[] = $lead->user_name;
                $row[] = strtotime($lead->user_assign_date) ? date('d-m-Y H:i:s', strtotime($lead->user_assign_date)) : '';
                $row[] = !is_null($lead->user) && !is_null($lead->user->getLeader) ? $lead->user->getLeader->name : '';
                $row[] = $follow1meet?date('d-m-Y', strtotime($follow1meet->meeting_date)):'';
                $row[] = $lead->status == 1?'open':'close';
                $row[] = $lead->lead_type;
                $row[] = $lead->action_date?date('d-m-Y', strtotime($lead->action_date)):'';
                
                fputcsv($file, $row);
            }

            fclose($file);
        };

        // Return the CSV as a stream
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="dump_leads.csv"',
        ]);
    }


    public function getLeadDataAjax(Request $request)
    {
        $user_id = '';
        $type = $request->type;

        if ($request->has('Platform')) {
            $userType = User::where('user_token', $request->input('key'))->first()->userRole->userRoleDetails->name;
            if ($userType != 'administrator' && $userType != 'employee') {
                $user_id = '';
                $user_id = User::where('teamlead', $user_id)->get()->pluck('id')->toArray();
            } else {
                $user_id = User::where('user_token', $request->input('key'))->first()->id;
            }
        } else {
            $userType = Auth::user()->userRole?->userRoleDetails?->name ?? 'employee';
            if ($userType != 'administrator'  && $userType != 'employee') {
                $user_id = User::where('teamlead', Auth::user()->id)->get()->pluck('id')->toArray();
            } else {
                $user_id = Auth::user()->id;
            }
        }

        $leadTable = ($type == "DumpLead") ? 'dump_leads1' : 'leads';

        if ($type == "Closed") {
            $query = Lead::leftJoin('users', 'users.id', 'leads.user_assigned_id')
                ->where('leads.status', 2)
                ->where('leads.status', '!=', 5)
                ->select('leads.*', 'users.name as user_name');
        } elseif ($type == "Open") {
            $query = Lead::leftJoin('users', 'users.id', 'leads.user_assigned_id')
                ->where('leads.status', 1)
                ->where('leads.status', '!=', 5)
                ->select('leads.*', 'users.name as user_name')
                ->distinct()
                ->orderBy('leads.updated_at', 'desc')
                ->whereNotIn('leads.id', function ($query) {
                    $query->select('lead_id')
                        ->from('lead_follow_up');
                });
        } elseif ($type == "InProcess") {
            $query = Lead::leftJoin('users', 'users.id', 'leads.user_assigned_id')
                ->join('lead_follow_up', 'lead_follow_up.lead_id', 'leads.id')
                ->where('leads.status', 1)
                ->where('leads.status', '!=', 5)
                ->select('leads.*', 'users.name as user_name')
                ->distinct();
        } elseif ($type == "DumpLead") {
            $query = DumpLead::leftJoin('users', 'users.id', 'dump_leads1.user_assigned_id')
                ->where('dump_leads1.status', 5)
                ->select('dump_leads1.*', 'users.name as user_name');
        } elseif ($type == "ReverseLead") {
            $query = Lead::leftJoin('users', 'users.id', 'leads.user_assigned_id')
                ->where('leads.lead_reverse', 1)
                ->select('leads.*', 'users.name as user_name');
        } else {
            $query = Lead::leftJoin('users', 'users.id', 'leads.user_assigned_id')
                ->where('leads.status', '!=', -1)->where('leads.status', '!=', 5)
                ->select('leads.*', 'users.name as user_name');
        }

        if ($type == 'Today' || $type == "TodayActivity" || $type == "TodayPending" || $type == "TodayClosed") {
            $query = $this->GetTodayData($type, $user_id, $userType);
        }

        if ($userType == 'employee') {
            $query = $query->where("{$leadTable}.user_assigned_id", $user_id);
        } elseif ($userType != 'administrator') {
            $query = $query->whereIn("{$leadTable}.user_assigned_id", (array)$user_id);
        }

        if ($request->filled('date') && $request->filled('end_date')) {
            $query = $query->where("{$leadTable}.updated_at", '>=', Carbon::parse($request->input('date'))->startOfDay())
                ->where("{$leadTable}.updated_at", '<=', Carbon::parse($request->input('end_date'))->endOfDay());
        } elseif ($request->filled('date')) {
            $query = $query->where("{$leadTable}.updated_at", '>=', Carbon::parse($request->input('date'))->startOfDay());
        } elseif ($request->filled('end_date')) {
            $query = $query->where("{$leadTable}.updated_at", '<=', Carbon::parse($request->input('end_date'))->endOfDay());
        }

        if ($request->filled('asign_id') && $request->input('asign_id') != "ALL") {
            $query = $query->where("{$leadTable}.user_assigned_id", $request->input('asign_id'));
        }

        if ($request->filled('action_date')) {
            $query = $query->whereDate("{$leadTable}.action_date", Carbon::parse($request->input('action_date'))->toDateString());
        }

        if ($request->filled('meet')) {
            if ($request->input('meet') === 'yes') {
                $query = $query->whereHas('latestMeetingFollowUp');
            } elseif ($request->input('meet') === 'no') {
                $query = $query->whereDoesntHave('latestMeetingFollowUp');
            }
        }

        $query = $query->with(['user.getLeader', 'latestFollowUp', 'latestMeetingFollowUp']);

        return DataTables::of($query)
            ->addColumn('checkbox', function ($lead) {
                if (auth()->user()->hasRole('administrator')) {
                    return '<input type="checkbox" name="moreassign" value="' . $lead->id . '">';
                } else {
                    return '';
                }
            })
            ->editColumn('name', function ($lead) use ($type) {
                if ($lead->id && $type != 'DumpLead') {
                    $leadUrl = url('leads/' . $lead->id);
                    return '<a  href="' . $leadUrl . '" class="text-primary fw-semibold">' . ucfirst($lead->name) . '</a>';
                } else {
                    return  ucfirst($lead->name);
                }
            })
            ->editColumn('created_at', function ($lead) use ($type) {
                return strtotime($lead->created_at) ? date('d-m-Y H:i:s', strtotime($lead->created_at)) : '';
            })
            ->editColumn('action_date', function ($lead) use ($type) {

                return strtotime($lead->action_date) && $lead->action_date != '0000-00-00 00:00:00' ? date('d-m-Y H:i:s', strtotime($lead->action_date)) : '';
            })
            ->editColumn('user_assign_date', function ($lead) use ($type) {
                return strtotime($lead->user_assign_date) ? date('d-m-Y H:i:s', strtotime($lead->user_assign_date)) : '';
            })
            ->editColumn('next_follow_up', function ($lead) use ($type) {
                $followUpDate = $lead->latestFollowUp;
                $date = $followUpDate ? $followUpDate->follow_up_date : '';
                return strtotime($date) && $date != '0000-00-00 00:00:00' ? date('d-m-Y H:i:s', strtotime($date)) : '';
            })
            ->editColumn('last_comment', function ($lead) use ($type) {
                $followUpDate = $lead->latestFollowUp;
                return '<label class="max-label">' . ($followUpDate ? $followUpDate->comment : '') . '</label>';
            })
            ->addColumn('team_leader', function ($lead) use ($type) {
                return isset($lead->user->getLeader->name) ? $lead->user->getLeader->name : '';
            })
            ->addColumn('meeting_date', function ($lead) use ($type) {
                $followMeet = $lead->latestMeetingFollowUp;
                return $followMeet && strtotime($followMeet->meeting_date) ? date('d-m-Y', strtotime($followMeet->meeting_date)) : '';
            })
            ->addColumn('status', function ($lead) use ($type) {
                return $lead->status == 1 ? 'open' : 'close';
            })
            ->addColumn('action', function ($lead) use ($type) {
                $items = '';
                $editUrl = url('edit_lead?id=' . $lead->id);
                $deleteUrl = url('delete_lead?id=' . $lead->id);
                $confirmTxt = "return confirm('Are you sure you want to delete this lead?')";
                $userRoleName = Auth::user()->userRole?->userRoleDetails?->name;

                if ($type != 'DumpLead' && $userRoleName != 'employee') {
                    $items .= '<li><a class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50" href="' . $editUrl . '"><i class="fa-regular fa-pen-to-square me-2 text-slate-400"></i> Edit</a></li>';
                    $items .= '<li><a class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 assignNewUserSingle" href="#" data-lead-id="' . $lead->id . '"><i class="fa-regular fa-user me-2 text-slate-400"></i> Assign New User</a></li>';
                    $items .= '<li><hr class="dropdown-divider my-1"></li>';
                    $items .= '<li><a class="dropdown-item py-1.5 px-3 flex items-center text-rose-600 hover:bg-rose-50 cursor-pointer" href="' . $deleteUrl . '" onclick="' . $confirmTxt . '"><i class="fa-regular fa-trash-can me-2 text-rose-500"></i> Delete</a></li>';
                }

                if ($items === '') {
                    return '';
                }

                return '<div class="dropdown text-end">
                    <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="fa-solid fa-ellipsis text-sm"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 150px;">' . $items . '</ul>
                </div>';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getAllLeadList(Request $request)
    {
        $type = null;
        $users = User::All();
        $followup = [];
        return view('leads.index', compact('type',  'users', 'followup'))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withCompanyname($this->settings->getCompanyName());
    }
    public function leadtype(Request $request, $type)
    {
        if ($request->has('Platform')) {
            $userType = User::where('user_token', $request->input('key'))->first()->userRole->userRoleDetails->name;
            if ($userType != 'administrator' && $userType != 'employee') {
                $user_id = '';
                $user_id = User::where('teamlead', $user_id)->get()->pluck('id')->toArray();
            } else {
                $user_id = User::where('user_token', $request->input('key'))->first()->id;
            }
        } else {
            $userType = Auth::user()->userRole->userRoleDetails->name;
            if ($userType != 'administrator'  && $userType != 'employee') {
                $user_id = User::where('teamlead', $user_id = Auth::user()->id)->get()->pluck('id')->toArray();
            } else {
                $user_id = Auth::user()->id;
            }
        }

        if ($type == "Closed") {
            $leads = Lead::join('users', 'users.id', 'leads.user_assigned_id')
                ->where('status', 2)
                ->where('leads.status', '!=', 5)
                ->select('leads.*', 'users.name as user_name')
                ->orderBy('leads.updated_at', 'desc')
                ->get();
        } elseif ($type == "Open") {
            $leads = Lead::join('users', 'users.id', 'leads.user_assigned_id')
                ->where('leads.status', 1)
                ->where('leads.status', '!=', 5)
                ->select('leads.*', 'users.name as user_name')
                ->distinct()
                ->orderBy('updated_at', 'desc')
                ->whereNotIn('leads.id', function ($query) {
                    $query->select('lead_id')
                        ->from('lead_follow_up');
                })
                ->get();
        } elseif ($type == "InProcess") {
            $leads = Lead::join('users', 'users.id', 'leads.user_assigned_id')
                ->join('lead_follow_up', 'lead_follow_up.lead_id', 'leads.id')
                ->where('leads.status', 1)
                ->where('leads.status', '!=', 5)
                ->select('leads.*', 'users.name as user_name')
                ->distinct()
                ->orderBy('leads.updated_at', 'desc')
                ->get();
        } elseif ($type == "DumpLead") {
            $leads = DumpLead::join('users', 'users.id', 'dump_leads1.user_assigned_id')
                ->join('lead_follow_up', 'lead_follow_up.lead_id', 'dump_leads1.id')
                ->where('dump_leads1.status', 5)
                ->select('dump_leads1.*', 'users.name as user_name')
                ->distinct()
                ->orderBy('leads.updated_at', 'desc')
                ->get();
        } elseif ($type == "ReverseLead") {
            $leads = Lead::where('leads.lead_reverse', 1)
                ->orderBy('lead_rev_date', 'desc')->get();
        } else {
            $leads = [];
        }


        if ($type == 'Today' || $type == "TodayActivity" || $type == "TodayPending" || $type == "TodayClosed") {
            $leads = $this->GetTodayData($type, $user_id, $userType);
        }


        if (!empty($leads) &&  $userType == 'employee') {
            if ($request->has('date') && $request->has('end_date')) {
                $date = $request->input('date');
                $e_date =  $request->input('end_date');
                $leads = $leads->where('user_assigned_id', $user_id)->where('updated_at', ">=", $date . "00:00:00")->where('updated_at', "<=", $e_date . "59:59:59");
            } else {
                $leads = $leads->where('user_assigned_id', $user_id);
            }
        } elseif (!empty($leads) &&  $userType != 'administrator') {
            if ($request->has('date') && $request->has('end_date')) {
                $date = $request->input('date');
                $e_date =  $request->input('end_date');
                $leads = $leads->whereIn('user_assigned_id', $user_id)->where('updated_at', ">=", $date . "00:00:00")->where('updated_at', "<=", $e_date . "59:59:59");
            } else {
                $leads = $leads->whereIn('user_assigned_id', $user_id);
            }
        } else {
            if ($request->has('date') && $request->has('end_date')) {
                $date = $request->input('date');
                $e_date =  $request->input('end_date');
                $leads = $leads->where('updated_at', ">=", $date . "00:00:00")->where('updated_at', "<=", $e_date . "59:59:59");
            }
        }
        if ($request->has('asign_id') && $request->input('asign_id') != "ALL") {
            $leads = $leads->where('user_assigned_id', $request->input('asign_id'));
        }





        $users = User::All();
        $followup = [];
        if ($request->has('Platform')) {
            $leads = $leads->select('id');
            $finalData = array("Lead_List" => $leads);
            return response()->json($finalData);
        } else {
            return view('leads.index', compact('type', 'leads', 'users', 'followup'))
                ->withUsers($this->users->getAllUsersWithDepartments())
                ->withCompanyname($this->settings->getCompanyName());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function multiple_ass_lead(Request $request)
    {
        //dd('dddddddddddd');
        foreach ($request->input('leadids') as $key => $value) {
            $this->leads->updateAssign($value, $request);
        }
        Session::flash('message', 'Update Successfully !');
        // return redirect()->back();
        return Redirect::back();
    }
    public function dump_multiple_ass_lead(Request $request)
    {
        foreach ($request->input('leadids') as $key => $value) {
            $this->leads->updateAssignDumpLead($value, $request);
        }
        Session::flash('message', 'Update Successfully !');
        return back();
    }

    public function NextFollowUp(Request $request, $id)
    {
        $input = $request->all();
        $lead_id = $id;
        $follow_up_status = $input['follow_up_status'];
        $follow_up_date = $input['follow_up_date'];
        $follow_up_time = $input['follow_up_time'];
        $comment        =   $input['comment'];
        $user_id = Auth::user()->id;
        $curdate = date("Y-m-d H:i:s");
        if ($follow_up_status != "In Process Lead") {
            $status = ($follow_up_status == "Closed Lead") ? 2 : 5;
            $lead = Lead::find($lead_id);
            $lead->status = $status;
            $lead->save();
        } else {
            $follow_up_status = "In Process Lead";
        }




        $date_time = $follow_up_date . ' ' . $follow_up_time;
        $LeadsFollowUpTableModel =  new LeadsFollowUpTableModel();
        $LeadsFollowUpTableModel->lead_id = $lead_id;
        $LeadsFollowUpTableModel->user_id = $user_id;
        $LeadsFollowUpTableModel->follow_up_date = $date_time;
        $LeadsFollowUpTableModel->follow_up_status = $follow_up_status;
        $LeadsFollowUpTableModel->comment = $comment;
        $LeadsFollowUpTableModel->action_date = $curdate;
        if ($LeadsFollowUpTableModel->save()) {
            $lead = Lead::find($lead_id);
            $lead->action_date = $curdate;
            $lead->save();
        }


        Session::flash('message', 'Added Successfully !');
        return back();
    }



    public function GetSeniors(Request $request)
    {

        $seniours = User::all();
        return $seniours;
    }

    public function MeetingType(Request $request)
    {

        $MeetingType = DB::table('tbl_meeting_type')->get();
        return $MeetingType;
    }
    public function Leadstatus(Request $request)
    {

        $leadstatus = DB::table('tbl_lead_status')->get();
        return $leadstatus;
    }

    public function StoreMeeting(Request $request, $id)
    {



        $input = $request->all();
        $lead_id = $id;

        $lead_follow_up_id = $input['lead_follow_up_id'];
        $seniors  = $input['seniors'];
        $lead_status = $input['lead_status'];
        $meeting_type = $input['meeting_type'];
        $comment     =  $input['comment'];
        $user_id = Auth::user()->id;
        $curdate = date("Y-m-d H:i:s");


        $LeadsFollowUpTableModel =  LeadsFollowUpTableModel::find($lead_follow_up_id);
        $LeadsFollowUpTableModel->lead_id = $lead_id;
        $LeadsFollowUpTableModel->user_id = $user_id;
        $LeadsFollowUpTableModel->action_date  = $curdate;
        $LeadsFollowUpTableModel->senior_visit  = $seniors;
        $LeadsFollowUpTableModel->follow_up_status  = $lead_status;
        $LeadsFollowUpTableModel->meeting_type = $meeting_type;
        $LeadsFollowUpTableModel->meeting_date = $curdate;
        $LeadsFollowUpTableModel->comment = $comment;
        if ($LeadsFollowUpTableModel->save()) {
            $lead = Lead::find($lead_id);
            $lead->action_date  = $curdate;
            $lead->save();
        }




        if ($lead_status == "Not Interested") {

            $lead_follow_up_id  = LeadsFollowUpTableModel::where('id', $lead_follow_up_id)->first();
            $lead_id = $lead_follow_up_id->lead_id;

            $lead = Lead::find($lead_id);
            $lead->status = 5;
            $lead->save();
        }



        Session::flash('message', 'Meeting Update Successfully !');
        return back();
    }







    /**
     * Data for Data tables
     * @return mixed
     */

    public function GetTodayData($type, $user_id, $userType)
    {
        if ($type == "Today") {
            $leads = Lead::join('users', 'users.id', 'leads.user_assigned_id')
                ->join('lead_follow_up', 'lead_follow_up.lead_id', 'leads.id')
                ->whereDate('leads.action_date', '!=', date('Y-m-d'))
                ->where('lead_follow_up.meeting_date', null)
                ->where('leads.status', '!=', 5)
                ->where('leads.status', '!=', 2)
                ->select('leads.*', 'users.name as user_name', DB::raw('(SELECT DATE_FORMAT(follow_up_date, \'%Y-%m-%d\') from lead_follow_up where lead_id  =  leads.id order by follow_up_date desc limit 1) as follow_up_date'))
                ->distinct()
                ->get();
            // ->count();
            $leads = $leads->where('follow_up_date', date('Y-m-d'));
        } elseif ($type == "TodayOpen" || $type == "TodayPending") {
            $leads = Lead::join('users', 'users.id', 'leads.user_assigned_id')
                ->join('lead_follow_up', 'lead_follow_up.lead_id', 'leads.id')
                ->where('lead_follow_up.meeting_date', null)
                ->whereDate('leads.action_date', '<', date('Y-m-d'))
                ->where('leads.status', '!=', 5)
                ->where('leads.status', '!=', 2)
                ->select('leads.*', 'users.name as user_name', DB::raw('(SELECT follow_up_date from lead_follow_up where lead_id  =  leads.id and leads.action_date < follow_up_date order by follow_up_date desc limit 1) as follow_up_date'))
                ->distinct()
                ->get();
            $leads = $leads->where('follow_up_date', '<', date('Y-m-d'));
            // ->count(); 
            return $leads;
        } elseif ($type == "TodayClosed") {
            $leads = Lead::join('users', 'users.id', 'leads.user_assigned_id')
                ->join('lead_follow_up', 'lead_follow_up.lead_id', 'leads.id')
                ->whereDate('lead_follow_up.action_date', date('Y-m-d'))
                ->whereIn('leads.status', [5, 2])
                ->select('leads.*')
                ->distinct()
                ->get();
        } elseif ($type == "TodayActivity") {
            $leads =  Lead::join('users', 'users.id', 'leads.user_assigned_id')
                ->join('lead_follow_up', 'lead_follow_up.lead_id', 'leads.id')
                ->whereDate('lead_follow_up.action_date', date('Y-m-d'))
                ->where('leads.status', '!=', 5)
                ->where('leads.status', '!=', 2)
                ->select('leads.*', 'users.name as user_name')
                ->distinct()
                ->get();
        } else {
            $leads = [];
        }
        if (!empty($leads) && $userType == 'employee') {
            $leads = $leads->where('user_assigned_id', $user_id);
        } elseif (!empty($leads) && $userType != 'administrator') {
            $leads = $leads->whereIn('user_assigned_id', $user_id);
        }
        return $leads;
    }


    public function getLeadDateWise(Request $request)
    {
        $type = null;
        $date = $request->input('date');
        $e_date =  $request->input('end_date');
        $leads = Lead::join('users', 'users.id', 'leads.user_assigned_id')->where('leads.status', '!=', 5)->where('leads.status', '!=', 5)->select('leads.*', 'users.name as user_name')
            ->whereBetween('leads.updated_at', [$date . ' 00:00:00', $e_date . ' 23:59:59'])
            ->orderBy('leads.updated_at', 'desc')
            ->get();

        $users = User::All();
        $followup = DB::select('SELECT max(meeting_date) as meeting_date,lead_id FROM `lead_follow_up` group by lead_id');

        $followupcomment = DB::select('SELECT max(follow_up_date) as follow_up_date,lead_id,comment FROM `lead_follow_up` group by lead_id');
        if (Auth::user()->userRole->userRoleDetails->name == 'employee') {
            $user_id = Auth::user()->id;

            $leads = $leads->where('user_assigned_id', $user_id)->where('updated_at', ">=", $date . "00:00:00")->where('updated_at', "<=", $e_date . "59:59:59");
        } elseif (Auth::user()->userRole->userRoleDetails->name != 'administrator') {
            $user_id = User::where('teamlead', Auth::user()->id)->get()->pluck('id')->toArray();
            $user_id[] = Auth::user()->id;
            $leads = $leads->whereIn('user_assigned_id', $user_id)->where('updated_at', ">=", $date . "00:00:00")->where('updated_at', "<=", $e_date . "59:59:59");
        }

        if ($request->has('asign_id') && $request->input('asign_id') != "" && $request->input('asign_id') != "ALL") {
            $leads = $leads->where('user_assigned_id', $request->input('asign_id'));
        }

        return view('leads.index', compact('type', 'leads', 'users', 'followup', 'followupcomment'))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withCompanyname($this->settings->getCompanyName());;
    }
    // public function getLeadAllData()
    // {

    //     $leads = Lead::select(
    //         ['id', 'name', 'contact_no', 'email', 'project', 'Budget', 'requirement', 'user_assigned_id', 'contact_date', 'created_at', 'status']
    //     )->get();
    //     return Datatables::of($leads)
    //         ->editColumn('name', function ($leads) {
    //             return '<a href="leads/' . $leads->id . '" ">' . $leads->name . '</a>';
    //         })
    //         ->editColumn('contact_no', function ($leads) {
    //             return $leads->contact_no;
    //         })
    //         ->editColumn('email', function ($leads) {
    //             return $leads->email;
    //         })
    //         ->editColumn('project', function ($leads) {
    //             return $leads->project;
    //         })

    //         ->editColumn('Budget', function ($leads) {
    //             return $leads->Budget;
    //         })
    //         ->editColumn('requirement', function ($leads) {
    //             return $leads->requirement;
    //         })
    //         ->editColumn('created_at', function ($leads) {
    //             return $leads->created_at;
    //         })




    //         //            ->editColumn('contact_date', function ($leads) {
    //         //                return $leads->contact_date ? with(new Carbon($leads->created_at))
    //         //                    ->format('d/m/Y') : '';
    //         //            })
    //         ->editColumn('user_assigned_id', function ($leads) {
    //             return $leads->user->name;
    //         })->editColumn('status', function ($leads) {
    //             global $status;
    //             $var = $leads->status;
    //             if ($var == 1) {
    //                 $status = 'open';
    //             } else {
    //                 $status = 'close';
    //             }
    //             return $status;
    //         })->escapeColumns([])
    //         ->make(true);
    // }
    public function anyData()
    {
        $leads = Lead::All();
        return Datatables::of($leads)
            ->editColumn('name', function ($leads) {
                return '<a href="leads/' . $leads->id . '" ">' . $leads->name . '</a>';
            })
            ->editColumn('contact_no', function ($leads) {
                return $leads->contact_no;
            })
            ->editColumn('email', function ($leads) {
                return $leads->email;
            })
            ->editColumn('project', function ($leads) {
                return $leads->project;
            })

            ->editColumn('Budget', function ($leads) {
                return $leads->Budget;
            })
            ->editColumn('requirement', function ($leads) {
                return $leads->requirement;
            })
            ->editColumn('created_at', function ($leads) {
                return $leads->created_at;
            })




            //            ->editColumn('contact_date', function ($leads) {
            //                return $leads->contact_date ? with(new Carbon($leads->created_at))
            //                    ->format('d/m/Y') : '';
            //            })
            ->editColumn('user_assigned_id', function ($leads) {
                return $leads->user->name;
            })->editColumn('status', function ($leads) {
                global $status;
                $var = $leads->status;
                if ($var == 1) {
                    $status = 'open';
                } else {
                    $status = 'close';
                }
                return $status;
            })->escapeColumns([])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {

        $project = DB::table('project_master')->where('status', '=', 1)->pluck('project_name', 'project_name')->reverse();
        $requirement = DB::table('requirement_master')->where('status', '=', 1)->orderBy('requirement_name', 'desc')->pluck('requirement_name', 'requirement_name')->reverse();
        $countries = DB::table('countries')->pluck('name', 'name');


        $source = DB::table('source_master')->where('status', '=', 1)->pluck('source_name', 'source_name')->reverse();
        //       $source= $source->reverse();
        $budget = DB::table('budget_master')->where('status', '=', 1)->pluck('budget_range', 'budget_range')->reverse();
        return view('leads.create', compact('project', 'requirement', 'source', 'countries', 'budget'))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withClients($this->clients->listAllClients());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLeadRequest|Request $request
     * @return \Illuminate\Http\Response
     */

    public function pushNotification()
    {

        $data =  DB::select("SELECT t3.name as empname,t2.id,follow_up_date,pushtoken,t2.name,t2.user_assigned_id FROM lead_follow_up t1 inner join leads t2 on t1.lead_id=t2.id inner join users t3 on t3.id=t2.user_assigned_id where meeting_date is null and date(t1.follow_up_date)=CURDATE() and pushtoken <> '' ");
        foreach ($data  as $data_notification) {
            //$optionBuilder = new OptionsBuilder();

            //$optionBuilder->setTimeToLive(60 * 20);

            //$notificationBuilder = new PayloadNotificationBuilder('Lead Followup');

            // $notificationBuilder->setBody('Dear ' . $data_notification->empname . ' Please follow up with ' . $data_notification->name . ' and close asap.' . $data_notification->id)->setSound('default');

            //$dataBuilder = new PayloadDataBuilder();
            //$dataBuilder->addData(['a_data' => 'my_data']);

            //$option = $optionBuilder->build();


            //$notification = $notificationBuilder->build();

            //$data = $dataBuilder->build();

            // new addition on 20 seo 2024//

            DB::table('app_notifications')->insert(
                array(
                    'user_id' => $data_notification->user_assigned_id,
                    'title' => 'lead followup',
                    'content' => 'Dear ' . $data_notification->empname . ' Please follow up with ' . $data_notification->name . ' and close asap.'
                )
            );


            if ($data_notification->pushtoken != '') {
                try {
                    $title = 'Lead Followup';
                    $body =  'Dear ' . $data_notification->empname . ' Please follow up with ' . $data_notification->name . ' and close asap.' . $data_notification->id;
                    $data = ['a_data' => 'my_data'];
                    FCMService::sendMessage($title, $body, $data_notification->pushtoken, $data);
                } catch (\Exception $e) {
                    //dd('in');
                }

                // $downstreamResponse = FCM::sendTo($data_notification->pushtoken, $option, $notification, $data);
                // $downstreamResponse->numberSuccess();
                // $downstreamResponse->numberFailure();
                // $downstreamResponse->numberModification();
                // $downstreamResponse->tokensToDelete();
                // $downstreamResponse->tokensToModify();
                // $downstreamResponse->tokensToRetry();
            }
        }
        return response()->json(['message' => "Notification sent successfully"]);
    }

    public function pushNotificationHour()
    {

        $data =  DB::select("SELECT t3.name as empname,t2.id,follow_up_date,pushtoken,t2.name,TIMESTAMPDIFF(MINUTE, NOW(),t1.follow_up_date),t2.user_assigned_id FROM 
       lead_follow_up t1 inner join leads t2 on t1.lead_id=t2.id inner join users t3 on t3.id=t2.user_assigned_id where meeting_date 
       is null and date(t1.follow_up_date)=CURDATE() and TIMESTAMPDIFF(MINUTE, NOW(),t1.follow_up_date)<60 and TIMESTAMPDIFF(MINUTE, NOW(),
       t1.follow_up_date)>0 and pushtoken <> '' ");
       
       //print_r($data);die;
        foreach ($data  as $data_notification) {
            //$optionBuilder = new OptionsBuilder();

            //$optionBuilder->setTimeToLive(60 * 20);

            //$notificationBuilder = new PayloadNotificationBuilder('Lead Followup');

            //$notificationBuilder->setBody('Dear '.$data_notification->empname.' Please follow up with '.$data_notification->name.' and close asap.')
            //->setSound('default');


            //            $notificationBuilder->setBody('Dear ' . $data_notification->empname . ' Please follow up with ' . $data_notification->name . ' and close asap.' . $data_notification->id)
            //              ->setSound('default');

            //        $dataBuilder = new PayloadDataBuilder();
            //      $dataBuilder->addData(['a_data' => 'my_data']);

            //    $option = $optionBuilder->build();

            // new addition on 20 seo 2024//

            DB::table('app_notifications')->insert(
                array(
                    'user_id' => $data_notification->user_assigned_id,
                    'title' => 'lead followup',
                    'content' => 'Dear ' . $data_notification->empname . ' Please follow up with ' . $data_notification->name . ' and close asap.'
                )
            );


            //            $notification = $notificationBuilder->build();

            //          $data = $dataBuilder->build();


            if ($data_notification->pushtoken != '') {
                try {
                    $token = $data_notification->pushtoken;
                    $title = 'Lead Followup';
                    $body =  'Dear ' . $data_notification->empname . ' Please follow up with ' . $data_notification->name . ' and close asap.' . $data_notification->id;
                    $data = ['a_data' => 'my_data'];
                    $fcmResponse = FCMService::sendMessage($title, $body, $token, $data);

                    //echo "success :- $fcmResponse <br>";
                } catch (\Exception $e) {
                    echo "error <br>";
                    //echo $e->getMessage();
                    //dd('in3');
                }
                // $downstreamResponse = FCM::sendTo($data_notification->pushtoken, $option, $notification, $data);
                // $downstreamResponse->numberSuccess();
                // $downstreamResponse->numberFailure();
                // $downstreamResponse->numberModification();
                // $downstreamResponse->tokensToDelete();
                // $downstreamResponse->tokensToModify();
                // $downstreamResponse->tokensToRetry();
            }
        }
        return response()->json(['message' => "Notification sent successfully"]);
    }




    public function store(StoreLeadRequest $request)
    {
        $check_mobile = LeadModel::where('contact_no', '=', $request->input('contact_no'))->first();
        if ($check_mobile != null) {
            $flag = 1;

            $project = DB::table('project_master')->where('status', '=', 1)->pluck('project_name', 'project_name')->reverse();
            $requirement = DB::table('requirement_master')->where('status', '=', 1)->orderBy('requirement_name', 'desc')->pluck('requirement_name', 'requirement_name')->reverse();
            $countries = DB::table('countries')->pluck('name', 'name');


            $source = DB::table('source_master')->where('status', '=', 1)->pluck('source_name', 'source_name')->reverse();
            //       $source= $source->reverse();
            $budget = DB::table('budget_master')->where('status', '=', 1)->pluck('budget_range', 'budget_range')->reverse();
            return view('leads.create', compact('project', 'requirement', 'source', 'countries', 'budget', 'flag'))
                ->withUsers($this->users->getAllUsersWithDepartments())
                ->withClients($this->clients->listAllClients());
        } else {


            $tok = User::where('id', '=', $request->input('user_assigned_id'))->first();
            $name = $tok->name;
            $date = $request->input('contact_date');
            $time = $request->input('contact_time');
            $combinedDT = date('Y-m-d H:i', strtotime("$date $time"));


            $getInsertedId = $this->leads->create($request);
            Session()->flash('flash_message', 'Lead is created');

            // $follow_up_data = new LeadsFollowUpTableModel();
            // $follow_up_data->lead_id = $getInsertedId;
            // $follow_up_data->user_id = $request->input('user_assigned_id');
            // $follow_up_data->follow_up_date = $combinedDT;
            // $follow_up_data->save();


            // $optionBuilder = new OptionsBuilder();

            // $optionBuilder->setTimeToLive(60 * 20);

            // $notificationBuilder = new PayloadNotificationBuilder('New Lead');

            // $notificationBuilder->setBody('Dear '.$name.' A New Lead Has been assigned to you,Please followup and close asap.'.$getInsertedId)
            //     ->setSound('default');

            // $dataBuilder = new PayloadDataBuilder();
            // $dataBuilder->addData(['a_data' => 'my_data']);

            //$option = $optionBuilder->build();

            // new addition on 20 seo 2024//

            DB::table('app_notifications')->insert(
                array(
                    'user_id' => $request->input('user_assigned_id'),
                    'title' => 'new lead',
                    'content' => 'Dear ' . $name . ' A New Lead Has been assigned to you,Please followup and close asap.'
                )
            );


            //$notification = $notificationBuilder->build();

            //$data = $dataBuilder->build();

            $tok = User::where('id', '=', $request->input('user_assigned_id'))->first();


            $token = $tok->pushtoken;
            //Log::info('FcmMessageController@notifyAll, token::=::'.print_r($notificationBuilder,true));


            if ($token != '') {
                $title = 'new lead';
                $body = 'Dear ' . $name . ' A New Lead Has been assigned to you,Please followup and close asap.';
                $response = null;
                try {
                    $response = FCMService::sendMessage($title, $body, $token);
                    //dd($response,'1');              
                } catch (\Exception $e) {
                    // dd($e->getMessage(), '2');
                }


                // $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
                // $downstreamResponse->numberSuccess();
                // $downstreamResponse->numberFailure();
                // $downstreamResponse->numberModification();

                //return Array - you must remove all this tokens in your database
                // $downstreamResponse->tokensToDelete();

                //return Array (key : oldToken, value : new token - you must change the token in your database )
                // $downstreamResponse->tokensToModify();

                //return Array - you should try to resend the message to the tokens in the array
                // $downstreamResponse->tokensToRetry();


                // Log::info('FcmMessageController@notifyAll, counts: '
                // . ' , Success: ' . print_r($downstreamResponse->numberSuccess(), true)
                // . ' , Fail: ' . print_r($downstreamResponse->numberFailure(), true)
                // . ' , Modification: ' . print_r($downstreamResponse->numberModification(), true)
                // );
            }
            return redirect(route('leads.show', $getInsertedId));
        }
    }

    public function updateAssign($id, Request $request)
    {
        

        $this->leads->updateAssign($id, $request);
        $tok = User::where('id', '=', $request->input('user_assigned_id'))->first();
        $name = $tok->name;

        // $optionBuilder = new OptionsBuilder();

        // $optionBuilder->setTimeToLive(60 * 20);

        // $notificationBuilder = new PayloadNotificationBuilder('New Lead');

        //$notificationBuilder->setBody('Dear ' . $name . ' A New Lead Has been assigned to you,Please followup and close asap.' . $id)
        //  ->setSound('default');

        //$dataBuilder = new PayloadDataBuilder();
        //$dataBuilder->addData(['a_data' => 'my_data']);

        //$option = $optionBuilder->build();


        //$notification = $notificationBuilder->build();

        //$data = $dataBuilder->build();

        $tok = User::where('id', '=', $request->input('user_assigned_id'))->first();
        

        $token = $tok->pushtoken;
        if ($token != '') {
            try {
                $title = 'New Lead';
                $body =  'Dear ' . $name . ' A New Lead Has been assigned to you,Please followup and close asap.' . $id;
                $data = ['a_data' => 'my_data'];
                $response = FCMService::sendMessage($title, $body, $token, $data);
                //dd($response,'1');              
            } catch (\Exception $e) {
              //  dd($e->getMessage(), '2');
            }
            // $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
            // $downstreamResponse->numberSuccess();
            // $downstreamResponse->numberFailure();
            // $downstreamResponse->numberModification();
            // $downstreamResponse->tokensToDelete();
            // $downstreamResponse->tokensToModify();
            // $downstreamResponse->tokensToRetry();
        }
        Session()->flash('flash_message', 'New user is assigned');
        return Redirect::back();
    }

    /**
     * Update the follow up date (Deadline)
     * @param UpdateLeadFollowUpRequest $request
     * @param $id
     * @return mixed
     */
    public function updateFollowup(UpdateLeadFollowUpRequest $request, $id)
    {
        $date =  $request->input('contact_date');
        $time =  $request->input('contact_time');
        $combinedDT = date('Y-m-d H:i:m', strtotime("$date $time"));

        //        $datetime = $request->input('contact_date') .' '.$request->input('contact_time');
        //        $datetime = new DateTime($datetime);
        //        dd($datetime);
        $follow_up = new LeadsFollowUpTableModel();
        $follow_up->follow_up_date = $combinedDT;
        $follow_up->comment = $request->input('comment');
        $follow_up->save();

        $this->leads->updateFollowup($id, $request);
        Session()->flash('flash_message', 'New follow up date is set');
        return Redirect::back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = LeadsFollowUpTableModel::where('lead_id', '=', $id)->orderBy('follow_up_date', 'desc')->get();
        $name = User::All();
        return view('leads.show', compact('data', 'name'))
            ->withLead($this->leads->find($id))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withCompanyname($this->settings->getCompanyName());
    }
    public function dumpShow($id)
    {

        $data = LeadsFollowUpTableModel::where('lead_id', '=', $id)->orderBy('follow_up_date', 'desc')->get();
        $name = User::All();
        $lead = DumpLead::find($id);
        return view('leads.show', compact('data', 'name', 'lead'))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withCompanyname($this->settings->getCompanyName());
    }

    /**
     * Complete lead
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateStatus($id, Request $request)
    {
        $this->leads->updateStatus($id, $request);
        Session()->flash('flash_message', 'Lead is completed');
        return Redirect::back();
    }
    public function newSellPage(Request $request)
    {
        $countries = DB::table('countries')->pluck('name', 'name');
        $project = DB::table('project_master')->where('status', '=', 1)->pluck('project_name', 'project_name')->reverse();

        $source = DB::table('source_master')->where('status', '=', 1)->pluck('source_name', 'source_name')->reverse();

        return view('leads.new_sell_lead_page', compact('countries', 'project', 'source'))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withClients($this->clients->listAllClients());
    }
    public function saveSellLead(Request $request)
    {
        $save_sell_lead = new SellLeadTableModel();
        $save_sell_lead->name = $request->input('name');
        $save_sell_lead->Contact_number = $request->input('number');
        $save_sell_lead->email = $request->input('email');
        $save_sell_lead->property_type = $request->input('property_type');
        $save_sell_lead->project = $request->input('project');
        $save_sell_lead->category = $request->input('category');
        $save_sell_lead->sub_category = $request->input('sub_category');
        $save_sell_lead->expected_price = $request->input('expected_price');
        $save_sell_lead->primium = $request->input('primium');
        $save_sell_lead->market_price = $request->input('market_price');
        $save_sell_lead->booking_price = $request->input('booking_price');
        $save_sell_lead->maintenance_charge = $request->input('maintenance_charge');
        $save_sell_lead->other_charge = $request->input('other_charge');
        $save_sell_lead->security_deposit = $request->input('security_deposit');
        $save_sell_lead->properry_status = $request->input('status');
        $save_sell_lead->disposition = $request->input('disposition');
        $save_sell_lead->source = $request->input('source');
        $save_sell_lead->property_tag = $request->input('property_tag');
        $save_sell_lead->address = $request->input('address');
        $save_sell_lead->block = $request->input('block');
        $save_sell_lead->country = $request->input('country');
        $save_sell_lead->state = $request->input('state');
        $save_sell_lead->city = $request->input('city');
        $save_sell_lead->locality = $request->input('locality');
        $save_sell_lead->super_area = $request->input('super_area');
        $save_sell_lead->plot_area = $request->input('plot_area');
        $save_sell_lead->cover_area = $request->input('cover_area');
        $save_sell_lead->carpet_area = $request->input('carpet_area');
        $save_sell_lead->bedrooms = $request->input('bedrooms');
        $save_sell_lead->bathroom = $request->input('bathroom');
        $save_sell_lead->work_station = $request->input('work_station');
        $save_sell_lead->cabins = $request->input('cabins');
        $save_sell_lead->facing = $request->input('facing');
        $save_sell_lead->furnish = $request->input('furnish');
        $save_sell_lead->floor = $request->input('floor');
        $save_sell_lead->built_year = $request->input('built_year');
        $save_sell_lead->car_parking = $request->input('car_parking');
        $save_sell_lead->remarks = $request->input('remarks');
        $save_sell_lead->description = $request->input('description');
        $save_sell_lead->other_feature = $request->input('other_feature');
        $save_sell_lead->status = '1';
        $save_sell_lead->save();
        Session()->flash('flash_message', 'Sell Lead is Created');
        return view('leads.sell_index');
    }
    public function getLeadData(Request $request)
    {
        $leads = SellLeadTableModel::All();
        return Datatables::of($leads)
            ->editColumn('name', function ($leads) {
                return '<a href="get_sell_view?id=' . $leads->id . '" class="text-primary fw-semibold text-capitalize">' . $leads->name . '</a>';
            })
            ->editColumn('Contact_number', function ($leads) {
                return $leads->Contact_number;
            })
            ->editColumn('email', function ($leads) {
                return $leads->email;
            })
            ->editColumn('project', function ($leads) {
                return $leads->project;
            })
            ->editColumn('property_type', function ($leads) {
                return $leads->property_type;
            })
            ->editColumn('booking_price', function ($leads) {
                return $leads->booking_price;
            })
            ->editColumn('bedrooms', function ($leads) {
                return $leads->bedrooms;
            })
            ->editColumn('created_at', function ($leads) {
                return strtotime($leads->created_at) ? date('d-m-Y H:i:s', strtotime($leads->created_at)) : '';
            })->escapeColumns([])
            ->make(true);

        //        ->editColumn('Budget', function ($leads) {
        //            return $leads->Budget;
        //        })
        //        ->editColumn('requirement', function ($leads) {
        //            return $leads->requirement;
        //        })




        //            ->editColumn('contact_date', function ($leads) {
        //                return $leads->contact_date ? with(new Carbon($leads->created_at))
        //                    ->format('d/m/Y') : '';
        //            })
        //        ->editColumn('user_assigned_id', function ($leads) {
        //            return $leads->user->name;
        //        })->make(true);
    }

    public function allSellList(Request $request)
    {
        return view('leads.sell_index');
    }

    // public function getAllLeadList(Request $request)
    // {
    //     $type = null;
    //     $user_id = Auth::user()->id;
    //     $leads = Lead::join('users', 'users.id', 'leads.user_assigned_id')->where('status', '!=', -1)->where('leads.status', '!=', 5)->select('leads.*', 'users.name as user_name')->orderBy('created_at', 'desc')->get();
    //     if (Auth::user()->userRole->userRoleDetails->name == 'employee') {
    //         $user_id = Auth::user()->id;
    //         $leads = $leads->where('user_assigned_id', $user_id);
    //     } elseif (Auth::user()->userRole->userRoleDetails->name != 'administrator') {
    //         $user_id = User::where('teamlead', $user_id)->get()->pluck('id')->toArray();
    //         $user_id[] = Auth::user()->id;
    //         $leads = $leads->whereIn('user_assigned_id', $user_id);
    //     }
    //     $users = User::All();

    //     $followupcomment = LeadsFollowUpTableModel::get();
    //     $followup = [];
    //     if ($request->has('asign_id') && $request->input('asign_id') != "ALL") {
    //         $leads = $leads->where('user_assigned_id', $request->input('asign_id'));
    //     }

    //     return view('leads.index', compact('type', 'leads', 'users', 'followup', 'followupcomment'))
    //         ->withUsers($this->users->getAllUsersWithDepartments())
    //         ->withCompanyname($this->settings->getCompanyName());
    // }


    public function getSellView(Request $request)
    {
        $get_view_data = SellLeadTableModel::where('id', '=', $request->input('id'))->first();
        return view('leads.sell_view_page', ['get_view_data' => $get_view_data])
            // ->withLead($this->leads->find(26) ?? null)
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withCompanyname($this->settings->getCompanyName());
    }

    public function deleteLead(Request $request)
    {
        $type = null;
        DB::table('leads')
            ->where('id', $request->input('id'))
            ->update(['status' => -1]);
        $leads = Lead::where('status', '!=', -1)->get();
        $users = User::All();
        $followup = DB::select('SELECT max(meeting_date) as meeting_date,lead_id FROM `lead_follow_up` group by lead_id');
        return Redirect::back();
        //comment by indrajeet
        //  return view('leads.index',['type'=>$type,'leads' => $leads,'users'=>$users,'followup'=>$followup])->render();
    }

    public function editLead(Request $id)
    {
        $leadId = $id instanceof Request ? ($id->input('id') ?: $id->input('update_id')) : $id;
        $user = Lead::findOrFail($leadId);
        $this->authorize('update', $user);
        $project = DB::table('project_master')->where('status', '=', 1)->pluck('project_name', 'project_name')->reverse();
        $requirement = DB::table('requirement_master')->where('status', '=', 1)->orderBy('requirement_name', 'desc')->pluck('requirement_name', 'requirement_name')->reverse();
        $countries = DB::table('countries')->pluck('name', 'name');

        $source = DB::table('source_master')->where('status', '=', 1)->pluck('source_name', 'source_name')->reverse();
        $budget = DB::table('budget_master')->where('status', '=', 1)->pluck('budget_range', 'budget_range')->reverse();
        return view('leads.create', compact('project', 'requirement', 'source', 'countries', 'budget', 'user'))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withClients($this->clients->listAllClients());
    }

    public function updateLead(Request $request)
    {
        $id = $request->input('update_id') ?: $request->input('id');
        $user = Lead::findOrFail($id);
        $this->authorize('update', $user);
        $user->fill($request->all());
        $user->save();

        $date = $request->input('contact_date');
        $time = $request->input('contact_time');
        $combinedDT = date('Y-m-d H:i', strtotime("$date $time"));

        $follow_up_data = new LeadsFollowUpTableModel();
        $follow_up_data->lead_id = $id;
        $follow_up_data->company_id = $user->company_id;
        $follow_up_data->user_id = $request->input('user_assigned_id');
        $follow_up_data->follow_up_date = $combinedDT;
        $follow_up_data->save();

        $tok = User::where('id', '=', $request->input('user_assigned_id'))->first();
        if ($tok) {
            $name = $tok->name;
            $token = $tok->pushtoken;
            if ($token != '') {
                try {
                    $title = 'New Lead';
                    $body =  'Dear ' . $name . ' A New Lead Has been assigned to you,Please followup and close asap.' . $id;
                    $data = ['a_data' => 'my_data'];
                    FCMService::sendMessage($title, $body, $token, $data);
                } catch (\Exception $e) {
                    // FCM error ignored
                }
            }
        }
        return redirect('lead_list');
    }

    /**
     * RESTful resource edit
     */
    public function edit($id)
    {
        $request = new Request(['id' => $id]);
        return $this->editLead($request);
    }

    /**
     * RESTful resource update
     */
    public function update($id, Request $request)
    {
        $request->merge(['update_id' => $id]);
        return $this->updateLead($request);
    }

    /**
     * RESTful resource destroy
     */
    public function destroy($id, Request $request)
    {
        $request->merge(['id' => $id]);
        return $this->deleteLead($request);
    }



    ///////////////////////////////////////////////////////////////////////////////////////////////
    public function bulkUploadIndex(Request $request)
    {
        $AlreadyExistContactNo = [];
        $CountValues = [];
        return view('leads.bulk_upload_index', compact('AlreadyExistContactNo', 'CountValues'));
    }


    public function bulkUploadCSVDownloadFormat(Request $request)
    {
        $file = storage_path() . '\bulkLeads\1608978059-import data 2.csv';
        //echo $file;
        //exit();
        if (file_exists($file)) {
            return  response()->download($file);
        } else {
            return back();
        }
    }

    public function bulkUploadStore(Request $request)
    {

        if (Input::hasFile('file')) {
            try {

                $datas = array();
                $CountValues = array();
                $values = array();
                $DBcolumn = array();
                $ContentData = array();
                $AlreadyExistContactNo = array();
                $SucessSavedCount = 0;
                $UnsucessSavedCount = 0;
                $InsertRecordCount = 0;
                $AlredyExistRecordCount = 0;

                $file = Input::file('file');
                $name = time() . '-' . $file->getClientOriginalName();
                $path = storage_path('bulkLeads');
                $file->move($path, $name);
                $filename = base_path('/storage/bulkLeads//' . $name);
                //echo $filename;
                //exit();

                chmod($filename, 0777);

                $file = fopen($filename, 'r');
                while (! feof($file)) {
                    $row = fgetcsv($file);
                    if ($row) {
                        $datas[] = $row;
                    }
                }
                foreach ($datas as $key => $value) {
                    if ($key == 0):
                        $DBcolumn[] = $value;
                    else:
                        $checkLeadMobile = Lead::where('contact_no', trim($value[1]))->first();
                        if ($checkLeadMobile == null):
                            $ContentData[] = $value;
                            $InsertRecordCount = $InsertRecordCount + 1;
                        else:
                            $AlreadyExistContactNo[] = $value[1];
                            $AlredyExistRecordCount = $AlredyExistRecordCount + 1;
                        endif;
                    endif;
                }
                foreach ($ContentData as  $data) {
                    foreach ($data as $key => $values) {
                        $save_value_array[$DBcolumn[0][$key]] = trim($values);
                    }
                    $LeadCreate = Lead::insert($save_value_array);
                    if ($LeadCreate) {
                        $SucessSavedCount = $SucessSavedCount + 1;
                    } else {
                        $UnsucessSavedCount = $UnsucessSavedCount + 1;
                    }
                }
                $CountValues['SucessSavedCount'] = $SucessSavedCount;
                $CountValues['UnsucessSavedCount'] = $UnsucessSavedCount;
                $CountValues['InsertRecordCount'] = $InsertRecordCount;
                $CountValues['AlredyExistRecordCount'] = $AlredyExistRecordCount;


                $request->session()->flash('success', 'Leads Import Csv Successfully!!');
                return view('leads.bulk_upload_index', compact('AlreadyExistContactNo', 'CountValues'));
            } catch (\Illuminate\Database\QueryException $e) {
                $request->session()->flash('success', 'Something is wrong');
                return back();
            }
        } else {
        }
        return back();
    }

    // Meeting and visits list
    public function all_meeting_and_visits(Request $request)
    {

        $userType = Auth::user()->userRole->userRoleDetails->name;
        
        if ($request->ajax()) {
            $type = $request->input('type') ?: 'meeting';
            $from = $request->input('from') ?: date('Y-m-d');
            $to = $request->input('to') ?: date('Y-m-d');

            $user_id = '';         
    
            if ($request->has('Platform')) {
                $userType = User::where('user_token', $request->input('key'))->first()->userRole->userRoleDetails->name;
                if ($userType != 'administrator' && $userType != 'employee') {
                    $user_id = '';
                    $user_id = User::where('teamlead', $user_id)->get()->pluck('id')->toArray();
                } else {
                    $user_id = User::where('user_token', $request->input('key'))->first()->id;
                }
            } else {
                $userType = Auth::user()->userRole->userRoleDetails->name;
                if ($userType != 'administrator'  && $userType != 'employee') {
                    $user_id = User::where('teamlead', $user_id = Auth::user()->id)->get()->pluck('id')->toArray();
                } else {
                    $user_id = Auth::user()->id;
                }
            }
    
            

            // Start building the base queryteam_leader
            $query = Lead::select(
                'leads.*',
                'users.name as user_name',
                DB::raw("(SELECT MAX(lead_follow_up.follow_up_date) FROM lead_follow_up WHERE lead_follow_up.lead_id = leads.id) AS nextfollowup"),
                DB::raw("(SELECT MAX(lead_follow_up.meeting_date) FROM lead_follow_up WHERE lead_follow_up.lead_id = leads.id) AS meetingdate")
            )
                ->join('users', 'users.id', '=', 'leads.user_assigned_id')
                ->join('lead_follow_up', 'lead_follow_up.lead_id', '=', 'leads.id')
                ->where('leads.status', '!=', 5);

            // Apply filters based on type
            if ($type === 'visit') {
                $query->whereBetween('lead_follow_up.meeting_date', [$from, $to])->where('meeting_type', 'Site visit');
            } elseif ($type === 'meeting') {
                $query->whereBetween('lead_follow_up.meeting_date', [$from, $to]);
            }

            if ($userType == 'employee') {
                $query = $query->where('leads.user_assigned_id', $user_id);
            } elseif ($userType != 'administrator') {
                $query = $query->whereIn('leads.user_assigned_id', $user_id);
            }

            

            $query->orderBy('lead_follow_up.meeting_date', 'DESC');
            // Use Yajra DataTables to handle the response
            return DataTables::of($query)
                ->editColumn('updated_at', function ($row) {
                    return  strtotime($row->updated_at) ? date('d-m-Y H:i:s', strtotime($row->updated_at)) : '';
                })
                ->editColumn('nextfollowup', function ($row) {
                    return  strtotime($row->nextfollowup) ? date('d-m-Y H:i:s', strtotime($row->nextfollowup)) : '';
                })
                ->editColumn('meetingdate', function ($row) {
                    return  strtotime($row->meetingdate) ? date('d-m-Y H:i:s', strtotime($row->meetingdate)) : '';
                })
                ->editColumn('meetingdate', function ($row) {
                    return  strtotime($row->meetingdate) ? date('d-m-Y H:i:s', strtotime($row->meetingdate)) : '';
                })
                ->addColumn('name', function ($row) use ($request) {
                    $leadLink = url("leads/{$row->id}");
                    return '<a href="' . $leadLink . '" target="_blank">' . e($row->name) . '</a>';
                })
                ->addColumn('team_leader', function ($row) {
                    return isset($row->user->getLeader->name) ? $row->user->getLeader->name : '';
                })
                ->addColumn('last_comment', function ($row) {
                    if($row->getfollowup && $row->getfollowup->first()){
                        return $row->getfollowup->first()->comment;
                    }
                    return '';
                })
                ->escapeColumns([])
                ->make(true);
        }
        return view('leads.meeting_visits')
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withCompanyname($this->settings->getCompanyName());;
    }

    public function send_to_dump_lead(Request $request)
    {
        $ids = $request->inputs ?: [];

        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided.', 'status' => 400]);
        }

        return DB::transaction(function () use ($ids) {
            // Fetch leads based on the provided IDs
            $leads = Lead::whereIn('id', $ids)->get();

            // Check if any leads were found
            if ($leads->isEmpty()) {
                return response()->json(['message' => 'No leads found for the provided IDs.', 'status' => 400]);
            }

            foreach ($leads as $lead) {
                $lead->status = 5;
                $lead->save();
            }

            //dd($lead->toArray());
            foreach ($leads as $lead) {
                DumpLead::create($lead->toArray());
            }

            Lead::whereIn('id', $ids)->delete();

            return response()->json(['message' => 'Lead Dump successfully!', 'status' => 200]);
        });
    }

    public function reverseLeadsUpdate()
    {
        //Lead reversed due to not response in 2 hours
        $leads = Lead::where('user_assign_date', '<', Carbon::now()->subHours(1))
            ->leftJoin('lead_follow_up', 'lead_follow_up.lead_id', '=', 'leads.id')
            ->whereNull('lead_follow_up.id')
            ->select('leads.*')
            ->whereNotNull('leads.user_assign_date')
            ->whereNotNull('leads.user_assigned_id')
            ->where('user_assign_date','>','2024-11-26')
            ->where('leads.status', '!=', 5)
            ->get()->map(function ($lead) {
                $userName = $lead->user ? $lead->user->name : '';
                $lead->user_assigned_id = null;
                $lead->user_assign_date = null;
                $lead->lead_reverse = 1;
                $lead->lead_rev_date = date('Y-m-d');
                $lead->reverse_remark = 'Lead reversed from ' . ($userName) . ' due to not response in 2 hours';
                $lead->save();
                return $lead;
            });
        echo 'Lead reversed successfully.';
    }

    public function reverseLeads()
    {
        return $this->reverseLeadsUpdate();
    }

    public function reverseVisitLeadsUpdate()
    {
        // Lead reversed due to no response in 15 days
        $leads = Lead::where('user_assign_date', '<', Carbon::now()->subDays(15)) // Changed to 15 days
            ->join('lead_follow_up', 'lead_follow_up.lead_id', '=', 'leads.id')
            ->select('leads.*')
            ->where('leads.status', '!=', 5)
            ->where('lead_follow_up.meeting_type', '!=', 'Site visit')
            ->where('user_assign_date','>','2024-11-26')
            ->whereNotNull('leads.user_assign_date')
            ->whereNotNull('leads.user_assigned_id')
            ->get()->map(function ($lead) {
                $userName = $lead->user ? $lead->user->name : '';
                $lead->user_assigned_id = null;
                $lead->user_assign_date = null;
                $lead->lead_reverse = 1;
                $lead->lead_rev_date = date('Y-m-d');
                $lead->reverse_remark = 'Lead reversed from ' . $userName . ' due to no visit in 15 days'; // Updated remark
                $lead->save();
                return $lead;
            });

       // dd($leads);
        echo 'Visit Lead reversed successfully.';
    }
    
     public function reverseVisitNoBookingLeadsUpdate()
    {
        // Lead reversed due to no response in 15 days
        $leads = Lead::where('meeting_date', '<', Carbon::now()->subDays(15)) // Changed to 15 days
            ->join('lead_follow_up', 'lead_follow_up.lead_id', '=', 'leads.id')
            ->select('leads.*')
            ->where('leads.status', '!=', 2)
            ->where('lead_follow_up.meeting_type', '=', 'Site visit')
            ->where('user_assign_date','>','2025-05-10')
            ->whereNotNull('leads.user_assign_date')
            ->whereNotNull('leads.user_assigned_id')
            ->whereNotNull('lead_follow_up.meeting_date')
            ->get()->map(function ($lead) {
                $userName = $lead->user ? $lead->user->name : '';
                $lead->user_assigned_id = null;
                $lead->user_assign_date = null;
                $lead->lead_reverse = 1;
                $lead->lead_rev_date = date('Y-m-d');
                $lead->reverse_remark = 'Lead reversed from ' . $userName . ' due to no booking after  visit in 15 days'; // Updated remark
                $lead->save();
                return $lead;
            });

       // dd($leads);
        echo 'Visit Lead reversed successfully.';
    }

    // public function getLeadGraphData(Request $request)
    // {
    //     // Get the requested year, defaulting to the current year if not provided
    //     $year = $request->input('year', Carbon::now()->year);

    //     // Query the total leads and closed leads per month for the given year
    //     $leadData = DB::table('leads')
    //         ->select(
    //             DB::raw('MONTH(created_at) as month'),
    //             DB::raw('COUNT(*) as total_leads'),
    //             DB::raw('SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as leads_closed')
    //         )
    //         ->whereYear('created_at', $year)
    //         ->groupBy(['month'])
    //         ->orderBy('month')
    //         ->get();

    //     // Format the data for the chart
    //     $totalLeads = array_fill(0, 12, 0); // Fill with 0s for each month
    //     $leadsClosed = array_fill(0, 12, 0); // Fill with 0s for each month

    //     foreach ($leadData as $data) {
    //         $index = $data->month - 1;
    //         $totalLeads[$index] = $data->total_leads;
    //         $leadsClosed[$index] = $data->leads_closed;
    //     }

    //     return response()->json([
    //         'totalLeads' => $totalLeads,
    //         'leadsClosed' => $leadsClosed,
    //     ]);
    // }

    public function getLeadGraphData(Request $request)
    {
        // Get the requested year, defaulting to the current year if not provided
        $year = $request->input('year', Carbon::now()->year);
    
        // Query the total leads and closed leads per month for the given year
        $leadData = DB::table('leads')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_leads'),
                DB::raw('SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as leads_closed')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    
        // Prepare the month names array for mapping
        $months = [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun", 
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];
    
        // Initialize the data arrays for "All Leads" and "Close" separately
        $allLeadsData = [];
        $closeLeadsData = [];
    
        // Initialize the values for each month for both "All Leads" and "Close"
        foreach ($months as $monthName) {
            $allLeadsData[] = [
                'leads' => 'All Leads',
                'month' => $monthName,
                'value' => 0, // Default value for all leads
            ];
            $closeLeadsData[] = [
                'leads' => 'Close',
                'month' => $monthName,
                'value' => 0, // Default value for closed leads
            ];
        }
    
        // Fill in the data from the database for "All Leads" and "Close"
        foreach ($leadData as $data) {
            $monthIndex = $data->month - 1; // Convert month number to array index
    
            // Set the "All Leads" (total_leads) value for the corresponding month
            $allLeadsData[$monthIndex]['value'] = $data->total_leads;
    
            // Set the "Close Leads" (leads_closed) value for the corresponding month
            $closeLeadsData[$monthIndex]['value'] = $data->leads_closed;
        }
    
        // Combine both the "All Leads" and "Close" data into one array, keeping the groups separate
        $formattedData = array_merge($allLeadsData, $closeLeadsData);
    
        // Return the data as a JSON response
        return response()->json($formattedData);
    }
    
    public function sendTeamReminders(Request $request){
            // reminder for Site Visit Not Done
            //$fcm = 'dOTNQi8MT-G8YecsL_anAh:APA91bHLDJCch28ZHpdLyAJ5YFNbct3X2HGHDY2isfHtJsvsU0P3Xr75uP707_sTcb_zXZhgwNbD2CNzeUrv52C7VQhewLnG7UNZSCeMAEsSE-5_29gZkWU';
             $leads = Lead::select(DB::raw('COUNT(leads.id) as leads_counts'),'leads.status', 'users.name','users.pushtoken','lead_follow_up.meeting_type')
             ->join('lead_follow_up', 'lead_follow_up.lead_id', '=', 'leads.id')
             ->join('users', 'users.id', 'leads.user_assigned_id')      
             ->where('lead_follow_up.follow_up_status','interested')
             ->where('lead_follow_up.meeting_type','!=','Site visit')
             ->where('leads.status','!=','2')           
             ->groupBy('user_id')->get();
             
            foreach($leads as $lead){
                try {
                    $title = 'Site Visit Reminder';
                    $body = "Dear {$lead->name}, You currently have {$lead->leads_counts} leads awaiting site visits.";
                    $data = ['a_data' => 'my_data'];
                    $fcm = $lead->pushtoken;
                    FCMService::sendMessage($title, $body, $fcm, $data);
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                }       
            } 
            // reminders for Booking            
            $leads = Lead::select(DB::raw('COUNT(leads.id) as leads_counts'),'leads.status', 'users.name','users.pushtoken','lead_follow_up.meeting_type')
            ->join('lead_follow_up', 'lead_follow_up.lead_id', '=', 'leads.id')
            ->join('users', 'users.id', 'leads.user_assigned_id')   
            ->where('lead_follow_up.meeting_type','=','Site visit')
            ->where('leads.status','!=','2')            
            ->groupBy('user_id')->get();
            
            foreach($leads as $lead){
                try {
                    $title = 'Site Visit Reminder';
                    $body = "Dear {$lead->name}, You currently have {$lead->leads_counts} leads whose site visits have been completed and are now awaiting booking.";
                    $data = ['a_data' => 'my_data'];
                    $fcm = $lead->pushtoken;
                    FCMService::sendMessage($title, $body, $fcm, $data);
                    //dd('done',$title, $body, $fcm, $data);
                } catch (\Exception $e) {
                    //dd($e->getMessage());
                }       
            }
            echo "Reminder Notifications Sent Successfully";
    }
    public function schedulerAutoAssign()
    {
        $toDate = date('Y-m-d');
        $fromDate = $toDate;
        $nowTime = date('H:i:s');
        $assignCount = 0;
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $Schedulers = Scheduler::whereRaw(
                "CONCAT(from_date, ' ', start_time) <= ?",
                [$now]
            )
            ->whereRaw(
                "CONCAT(to_date, ' ', end_time) >= ?",
                [$now]
            )
            ->get();
  
           if(!count($Schedulers)){
           return response()->json([
                'status'  => 404,
                'message' => 'No active scheduler found for current time.',
            ]); 
        }
        foreach($Schedulers as $scheduler){
            if(empty($scheduler->user_ids)){
                //
            }else{
                $userIds =  json_decode($scheduler->user_ids);
                
                if (empty($userIds)) {
                    continue;
                }
                $toDate = date('Y-m-d', strtotime($scheduler->to_date));
                $fromDate = date('Y-m-d', strtotime($scheduler->from_date)) ;
                $fromDateTime = date('Y-m-d H:i:s', strtotime($fromDate . ' ' . $scheduler->start_time));
                $toDateTime = date('Y-m-d H:i:s', strtotime($toDate . ' ' . $scheduler->end_time));
                
                $leads = Lead::where(function ($query) {
                    $query->whereNull('user_assigned_id')
                          ->orWhere('user_assigned_id', 0)
                          ->orWhere('user_assigned_id', 1);
                })
                ->whereBetween('created_at', [$fromDateTime, $toDateTime]);
                // ->whereDate('created_at', '>=', $fromDateTime)
                // ->whereDate('created_at', '<=', $toDateTime);
                // ->whereDate('created_at', '<=', $toDate)
                // ->whereDate('created_at', '>=', $fromDate);
                if($scheduler->projects && $scheduler->projects != '[""]'){
                  $leads =  $leads->whereIn('project', json_decode($scheduler->projects));
                }
               $leads = $leads->get();
                if ($leads->isEmpty()) {
                    continue;
                }

                $startIndex  =  $scheduler->sec_index; 
                $userCount   =  count($userIds);
                $assignedAt  = date('Y-m-d H:i:s');
                $currentIndex   = $startIndex;
                
                if ($startIndex >= $userCount) {
                    $startIndex   = 0;
                    $currentIndex = 0;
                }
                foreach ($leads as $lead) {
                    $assignedUserId = $userIds[$currentIndex % $userCount];
                    $assignUser = [
                        'user_assigned_id' => $assignedUserId,
                        'user_assign_date' => $assignedAt,
                    ];
                   
                    $this->leads->updateAssign($lead->id, collect($assignUser));
                    $currentIndex++;
                    $assignCount++;

                    $scheduler->sec_index = $currentIndex % $userCount;
                    $scheduler->save();
                }
            }
                    
        }
        return response()->json([
            'status'  => 200,
            'message' => "{$assignCount} leads assigned to users successfully.",
        ]);
    }
}