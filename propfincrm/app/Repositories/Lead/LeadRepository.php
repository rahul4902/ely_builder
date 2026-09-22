<?php
namespace App\Repositories\Lead;

use App\Models\DumpLead;
use App\Models\Lead;
use Notifynder;
use Carbon;
use DB;
use Auth;
use App\Models\User;
use App\Services\FCMService;
use App\Services\LeadLifecycle;
/**
 * Class LeadRepository
 * @package App\Repositories\Lead
 */
class LeadRepository implements LeadRepositoryContract
{
    /**
     *
     */
    const CREATED = 'created';
    /**
     *
     */
    const UPDATED_STATUS = 'updated_status';
    /**
     *
     */
    const UPDATED_DEADLINE = 'updated_deadline';
    /**
     *
     */
    const UPDATED_ASSIGN = 'updated_assign';

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Lead::findOrFail($id);
    }

    /**
     * @param $requestData
     * @return mixed
     */
    public function create($requestData)
    {
        $client_id = $requestData->get('client_id');
        $input = $requestData = array_merge(
            $requestData->all(),
            ['user_created_id' => \Auth::id(),
                'contact_date' => $requestData->contact_date . " " . $requestData->contact_time . ":00",
                ]
        );

        $lead = Lead::create($input);
        $insertedId = $lead->id;
        Session()->flash('flash_message', 'Lead successfully added!');

        event(new \App\Events\LeadAction($lead, self::CREATED));

        return $insertedId;
    }

    /**
     * @param $id
     * @param $requestData
     */
    public function updateStatus($id, $requestData)
    {
        $lead = Lead::findOrFail($id);
        app(LeadLifecycle::class)->markWon($lead);
        $lead->save();
        event(new \App\Events\LeadAction($lead, self::UPDATED_STATUS));
    }

    /**
     * @param $id
     * @param $requestData
     */
    public function updateFollowup($id, $requestData)
    {
        $lead = Lead::findOrFail($id);
        $input = $requestData->all();
        $input = $requestData =
            ['contact_date' => $requestData->contact_date . " " . $requestData->contact_time . ":00"];
        $lead->fill($input)->save();
        event(new \App\Events\LeadAction($lead, self::UPDATED_DEADLINE));
    }

    /**
     * @param $id
     * @param $requestData
     */
    public function updateAssign($id, $requestData)
    {
        
        $lead = Lead::findOrFail($id);
        $requestData['status'] = 1;
        $input = $requestData->get('user_assigned_id');
        
        $input = array_replace($requestData->all());
        $input['lead_reverse'] = 0;
        $input['lead_rev_date'] = NULL;
        $input['reverse_remark'] = NULL;
        $input['user_assign_date'] = date('Y-m-d H:i:s');
        $lead->fill($input)->save();
        $insertedName = $lead->user->name;

        event(new \App\Events\LeadAction($lead, self::UPDATED_ASSIGN));
        try {
        $tok = User::where('id', '=', $requestData->get('user_assigned_id'))->first();
        $name = $tok->name;
        DB::table('app_notifications')->insert(
            array(
                'user_id' => $requestData->get('user_assigned_id'),
                'title' => 'new lead',
                'content' => 'Dear ' . $name . ' A New Lead Has been assigned to you,Please followup and close asap.'
            )
        );
        
        $token = $tok->pushtoken;
        $title = 'new lead';
        $body = 'Dear ' . $name . ' A New Lead Has been assigned to you,Please followup and close asap.';
        $response = FCMService::sendMessage($title, $body, $token);
        } catch (\Exception $e) {
//             dd($e->getMessage(), $e->getLine());
        }

        // $dumpLead = new DumpLead();
        // $dumpLead->fill($lead->toArray());
        // $dumpLead->makeHidden(['id']);
        // $dumpLead->save();
        // $lead->delete();
    }

    public function updateAssignDumpLead($id, $requestData)
    {
        $dumpLead = DumpLead::findOrFail($id);
        $requestData['status'] = 1;
        $input = $requestData->get('user_assigned_id');
        $input = array_replace($requestData->all());
       
        $dumpLead->fill($input)->save();
        $insertedName = $dumpLead->user->name;      

        event(new \App\Events\DumpLeadAction($dumpLead, self::UPDATED_ASSIGN));
        
         try {
            $tok = User::where('id', '=', $requestData->get('user_assigned_id'))->first();
            $name = $tok->name;
            DB::table('app_notifications')->insert(
                array(
                    'user_id' => $requestData->get('user_assigned_id'),
                    'title' => 'new lead',
                    'content' => 'Dear ' . $name . ' A New Lead Has been assigned to you,Please followup and close asap.'
                )
            );
            
            $token = $tok->pushtoken;
            $title = 'new lead';
            $body = 'Dear ' . $name . ' A New Lead Has been assigned to you,Please followup and close asap.';
            $response = FCMService::sendMessage($title, $body, $token);
        } catch (\Exception $e) {
             //dd($e->getMessage(), $e->getLine());
        }


        $lead = new Lead();
        $lead->fill($dumpLead->toArray());
        //$lead->makeHidden(['id']);
        $lead->save();
        $dumpLead->delete();
    }

    /**
     * @return int
     */
    public function leads()
    {
        return Lead::join('users','users.id','leads.user_assigned_id')->where('status','!=',5)->count();
    }

    public function leadsByRole($user_id)
    { 
        if(Auth::user()->userRole->userRoleDetails->name == 'employee'){
            $user_id=Auth::user()->id; 
            return Lead::where('user_assigned_id',$user_id)->where('status','!=',5)->count();
        } elseif(Auth::user()->userRole->userRoleDetails->name != 'administrator'){
            $user_id=User::where('teamlead',$user_id)->get()->pluck('id')->toArray();  
            return Lead::whereIn('user_assigned_id',$user_id)->where('status','!=',5)->count();
        }
        return Lead::join('users','users.id','leads.user_assigned_id')->where('user_created_id',$user_id)->count();
    }

    /**
     * @return mixed
     */
    public function allOpenLeads()
    {
        return Lead::join('users','users.id','leads.user_assigned_id')->where('status', 1)->where('status','!=',5)->count();
    }

    public function allOpenLeadsByRole($user_id)
    {
        if(Auth::user()->userRole->userRoleDetails->name == 'employee'){
            $user_id=Auth::user()->id; 
            return Lead::where('user_assigned_id',$user_id)->where('status','!=',5)->where('status', 1)->count();
        } elseif(Auth::user()->userRole->userRoleDetails->name != 'administrator'){
            $user_id=User::where('teamlead',$user_id)->get()->pluck('id')->toArray();  
            return Lead::whereIn('user_assigned_id',$user_id)->where('status','!=',5)->where('status', 1)->count();
        }
        return Lead::where('status', 1)->where('user_created_id',$user_id)->count();
    }

    /**
     * @return mixed
     */
    public function allCompletedLeads()
    {
        return Lead::join('users','users.id','leads.user_assigned_id')->where('status','!=',5)->where('status', 2)->count();
    }

    public function allCompletedLeadsByRole($user_id)
    {
        if(Auth::user()->userRole->userRoleDetails->name == 'employee'){
            $user_id=Auth::user()->id; 
            return Lead::where('user_assigned_id',$user_id)->where('status','!=',5)->where('status', 2)->count();
        } elseif(Auth::user()->userRole->userRoleDetails->name != 'administrator'){
            $user_id=User::where('teamlead',$user_id)->get()->pluck('id')->toArray();  
            return Lead::whereIn('user_assigned_id',$user_id)->where('status','!=',5)->where('status', 2)->count();
        }
        return Lead::where('status', 2)->where('user_created_id',$user_id)->count();
    }


    public function allInProcessLeads()
    {
        return Lead::join('lead_follow_up','lead_follow_up.lead_id','leads.id')->join('users','users.id','leads.user_assigned_id')->where('leads.status', 1)->where('leads.status','!=',5)->distinct()->count('leads.id');
    }




    public function allInProcessLeadsByRole($user_id)
    {
        if(Auth::user()->userRole->userRoleDetails->name == 'employee'){
            $user_id=Auth::user()->id; 
            return Lead::join('lead_follow_up','lead_follow_up.lead_id','leads.id')->where('leads.user_assigned_id',$user_id)->where('leads.status', 1)->where('leads.status','!=',5)->distinct()->count('leads.id');
        } elseif(Auth::user()->userRole->userRoleDetails->name != 'administrator'){
            $user_id=User::where('teamlead',$user_id)->get()->pluck('id')->toArray();  
            return Lead::join('lead_follow_up','lead_follow_up.lead_id','leads.id')->whereIn('leads.user_assigned_id',$user_id)->where('leads.status', 1)->where('leads.status','!=',5)->distinct()->count('leads.id');
        }        
    }

    /**
     * @return float|int
     */
    public function percantageCompleted()
    {
        if (!$this->leads() || !$this->allCompletedLeads()) {
            $totalPercentageLeads = 0;
        } else {
            $totalPercentageLeads = $this->allCompletedLeads() / $this->leads() * 100;
        }

        return $totalPercentageLeads;
    }

    /**
     * @return mixed
     */
    public function completedLeadsToday()
    {
        return Lead::whereRaw(
            'date(updated_at) = ?',
            [Carbon::now()->format('Y-m-d')]
        )->where('status', 2)->count();
    }

    /**
     * @return mixed
     */
    public function createdLeadsToday()
    {
        return Lead::whereRaw(
            'date(created_at) = ?',
            [Carbon::now()->format('Y-m-d')]
        )->count();
    }

    /**
     * @return mixed
     */
    public function completedLeadsThisMonth()
    {
        return DB::table('leads')
            ->select(DB::raw('count(*) as total, updated_at'))
            ->where('status', 2)
            ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()])->get();
    }

    /**
     * @return mixed
     */
    public function createdLeadsMonthly()
    {
        return DB::table('leads')
            ->select(DB::raw('count(*) as month, updated_at'))
            ->where('status', 2)
            ->groupBy(DB::raw('YEAR(updated_at), MONTH(updated_at)'))
            ->get();
    }

    /**
     * @return mixed
     */
    public function completedLeadsMonthly()
    {
        return DB::table('leads')
            ->select(DB::raw('count(*) as month, created_at'))
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function totalOpenAndClosedLeads($id)
    {
        $open_leads = Lead::where('status', 1)
        ->where('user_assigned_id', $id)
        ->count();

        $closed_leads = Lead::where('status', 2)
        ->where('user_assigned_id', $id)->count();

        return collect([$closed_leads, $open_leads]);
    }




    public function leadsToday()
    {

        $leads = Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id')  
                 ->whereDate('leads.action_date','!=' ,date('Y-m-d'))  
                ->where('leads.status','!=',5)
			->where('leads.status','!=',2)
                ->select('leads.*',DB::raw('(SELECT DATE_FORMAT(follow_up_date, \'%Y-%m-%d\') from lead_follow_up where lead_id  =  leads.id order by follow_up_date desc limit 1) as follow_up_date'))                
                ->distinct('leads.id')
                ->get();
                // ->count();
                $leads = $leads->where('follow_up_date',date('Y-m-d'))->count() ;
                // $leads = $leads->where('leads',date('Y-m-d'))->count() ;
                return $leads;
    }
 
    public function allCompletedLeadsToday()
    {
        return Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id')  
                ->whereDate('lead_follow_up.action_date', date('Y-m-d'))  
                ->whereIn('leads.status',[5,2]) 
                ->distinct()
                ->count('leads.id');  
    }
    public function allInProcessLeadsToday()
    {

        $leads = Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id')   
                ->where('lead_follow_up.meeting_date', null) 
                ->where('leads.status','!=', 2) 
                ->where('leads.status','!=' ,5) 
                ->whereDate('leads.action_date', '<', date('Y-m-d')) 
                ->where('leads.status','!=',5)
                ->select('leads.*',DB::raw('(SELECT follow_up_date from lead_follow_up where lead_id  =  leads.id and leads.action_date < follow_up_date order by follow_up_date desc limit 1) as follow_up_date')) 
                ->distinct('leads.id')
                ->get();
                $leads = $leads->where('follow_up_date','<',date('Y-m-d'))->count() ; 
                // ->count(); 
                return $leads;
 
    }


    public function allActivityToday()
    {
        return Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id') 
                ->whereDate('lead_follow_up.action_date', date('Y-m-d'))  
                ->where('leads.status','!=',5) 
                ->distinct()
                ->count('leads.id');   
    }


    public function leadsByRoleToday($user_id)
    {  
        if(Auth::user()->userRole->userRoleDetails->name == 'employee'){
            $user_id=Auth::user()->id; 
            $leads= Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id')
                ->where('leads.user_assigned_id',$user_id)
                ->whereDate('leads.action_date','!=' ,date('Y-m-d')) 
                ->where('lead_follow_up.meeting_date',null) 
                ->where('leads.status','!=',5)
				 ->where('leads.status','!=',2)
                ->select('leads.*',DB::raw('(SELECT DATE_FORMAT(follow_up_date, \'%Y-%m-%d\') from lead_follow_up where lead_id  =  leads.id order by follow_up_date desc limit 1) as follow_up_date'))                
                ->distinct('leads.id')
                ->get();
                // dd( $leads);
                // ->count();
                $leads = $leads->where('follow_up_date',date('Y-m-d'))->count() ;
               
                return $leads;
            
        } elseif(Auth::user()->userRole->userRoleDetails->name != 'administrator'){
            $user_id=User::where('teamlead',$user_id)->get()->pluck('id')->toArray();  
             $leads= Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id')
                ->whereIn('leads.user_assigned_id',$user_id) 
                ->whereDate('leads.action_date','!=' ,date('Y-m-d')) 
                ->where('lead_follow_up.meeting_date',null) 
                ->where('leads.status','!=',5)
				 ->where('leads.status','!=',2)
                ->select('leads.*',DB::raw('(SELECT DATE_FORMAT(follow_up_date, \'%Y-%m-%d\') from lead_follow_up where lead_id  =  leads.id order by follow_up_date desc limit 1) as follow_up_date'))                
                ->distinct('leads.id')
                ->get();
                // ->count();
                $leads = $leads->where('follow_up_date',date('Y-m-d'))->count() ;
                return $leads;
        }
          
    } 
    public function allCompletedLeadsByRoleToday($user_id)
    {
        if(Auth::user()->userRole->userRoleDetails->name == 'employee'){
            $user_id=Auth::user()->id; 
            return Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id') 
                ->where('leads.user_assigned_id',$user_id)
                ->whereDate('lead_follow_up.action_date', date('Y-m-d')) 
                ->whereIn('leads.status',[5,2]) 
                ->distinct()
                ->count('leads.id');  
            
        } elseif(Auth::user()->userRole->userRoleDetails->name != 'administrator'){
            $user_id=User::where('teamlead',$user_id)->get()->pluck('id')->toArray();  
            return Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id') 
                ->whereIn('leads.user_assigned_id',$user_id)
                ->whereDate('lead_follow_up.action_date', date('Y-m-d'))  
                ->whereIn('leads.status',[5,2]) 
                ->distinct()
                ->count('leads.id');    
        }
        
    }
    public function allInProcessLeadsByRoleToday($user_id)
    {
        if(Auth::user()->userRole->userRoleDetails->name == 'employee'){
            $user_id=Auth::user()->id; 
              $leads = Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id') 
                ->where('leads.user_assigned_id',$user_id) 
                ->where('lead_follow_up.meeting_date', null) 
                ->whereDate('leads.action_date', '<', date('Y-m-d'))
				//->whereDate('leads.action_date', '<', date('Y-m-d')) 
                ->where('leads.status','!=',5)
				  ->where('leads.status','!=',2)
                ->select('leads.*',DB::raw('(SELECT follow_up_date from lead_follow_up where lead_id  =  leads.id and leads.action_date < follow_up_date order by follow_up_date desc limit 1) as follow_up_date')) 
                ->distinct('leads.id')
                ->get();
                $leads = $leads->where('follow_up_date','<',date('Y-m-d'))->count() ; 
                // ->count(); 
                return $leads;
            
        } elseif(Auth::user()->userRole->userRoleDetails->name != 'administrator'){
            $user_id=User::where('teamlead',$user_id)->get()->pluck('id')->toArray();  
             $leads = Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id') 
                ->whereIn('leads.user_assigned_id',$user_id) 
                ->where('lead_follow_up.meeting_date', null) 
                  ->whereDate('leads.action_date', '<', date('Y-m-d')) 
                ->where('leads.status','!=',5)
				 ->where('leads.status','!=',2)
               ->select('leads.*',DB::raw('(SELECT follow_up_date from lead_follow_up where lead_id  =  leads.id and leads.action_date < follow_up_date order by follow_up_date desc limit 1) as follow_up_date')) 
                ->distinct('leads.id')
                ->get();
                $leads = $leads->where('follow_up_date','<',date('Y-m-d'))->count() ; 
                // ->count(); 
                return $leads;
        }
    }



    public function allActivityTodayByRole($user_id)
    {
        if(Auth::user()->userRole->userRoleDetails->name == 'employee'){
            $user_id=Auth::user()->id; 
            return Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id') 
                ->where('leads.user_assigned_id',$user_id)
                ->whereDate('lead_follow_up.action_date', date('Y-m-d'))  
                ->where('leads.status','!=',5)
				  ->where('leads.status','!=',2)
                ->distinct()
                ->count('leads.id');  
            
        } elseif(Auth::user()->userRole->userRoleDetails->name != 'administrator'){
            $user_id=User::where('teamlead',$user_id)->get()->pluck('id')->toArray();  
            return Lead::join('users','users.id','leads.user_assigned_id')
                ->join('lead_follow_up','lead_follow_up.lead_id','leads.id') 
                ->whereIn('leads.user_assigned_id',$user_id)
                ->whereDate('lead_follow_up.action_date', date('Y-m-d'))  
                ->where('leads.status','!=',5)
				  ->where('leads.status','!=',2)
                ->distinct()
                ->count('leads.id'); 
        }
    }

        
    
}
