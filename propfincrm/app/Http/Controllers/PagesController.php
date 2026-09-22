<?php
namespace App\Http\Controllers;

use DB;
use Carbon;
use App\Http\Requests;
use Auth;
use App\Repositories\Task\TaskRepositoryContract;
use App\Repositories\Lead\LeadRepositoryContract;
use App\Repositories\User\UserRepositoryContract;
use App\Repositories\Client\ClientRepositoryContract;
use App\Repositories\Setting\SettingRepositoryContract;

class PagesController extends Controller
{

    protected $users;
    protected $clients;
    protected $settings;
    protected $tasks;
    protected $leads;

    public function __construct(
		
        UserRepositoryContract $users,
        ClientRepositoryContract $clients,
        SettingRepositoryContract $settings,
        TaskRepositoryContract $tasks,
        LeadRepositoryContract $leads
    ) {
		
        $this->users = $users;
        $this->clients = $clients;
        $this->settings = $settings;
        $this->tasks = $tasks;
        $this->leads = $leads;
    }

    /**
     * Dashobard view
     * @return mixed
     */
    public function dashboard()
    {
//dd(auth()->user());
      /**
         * Other Statistics
         *
         */
        $companyname = $this->settings->getCompanyName();
        $users = $this->users->getAllUsers();
        $totalClients = $this->clients->getAllClientsCount();
        $totalTimeSpent = $this->tasks->totalTimeSpent();

     /**
      * Statistics for all-time tasks.
      *
      */
        
        $alltasks = $this->tasks->tasks();
        $allCompletedTasks = $this->tasks->allCompletedTasks();
        $totalPercentageTasks = $this->tasks->percantageCompleted();

     /**
      * Statistics for today tasks.
      *
      */
        $completedTasksToday =  $this->tasks->completedTasksToday();
        $createdTasksToday = $this->tasks->createdTasksToday();

     /**
      * Statistics for tasks this month.
      *
      */
         $taskCompletedThisMonth = $this->tasks->completedTasksThisMonth();
    

     /**
      * Statistics for tasks each month(For Charts).
      *
      */
        $createdTasksMonthly = $this->tasks->createdTasksMothly();
        $completedTasksMonthly = $this->tasks->completedTasksMothly();

     /**
      * Statistics for all-time Leads.
      *
      */
         //dd(Auth::user()->userRole->userRoleDetails); 
        if(Auth::user()->userRole->userRoleDetails->name != 'administrator'){
			//dd("admin");
            $allleads = $this->leads->leadsByRole(Auth::user()->id);  
            $allOpenLeads = $this->leads->allOpenLeadsByRole(Auth::user()->id);
            $allCompletedLeads = $this->leads->allCompletedLeadsByRole(Auth::user()->id);
            $allInProcessLeads = $this->leads->allInProcessLeadsByRole(Auth::user()->id);

            $Todayallleads = $this->leads->leadsByRoleToday(Auth::user()->id);  
            $TodayallCompletedLeads = $this->leads->allCompletedLeadsByRoleToday(Auth::user()->id);
            $TodayallInProcessLeads = $this->leads->allInProcessLeadsByRoleToday(Auth::user()->id);
            $TodayallActivityLeads = $this->leads->allActivityTodayByRole(Auth::user()->id);


        }else{
//			dd("else");
            $allleads = $this->leads->leads();

            $allOpenLeads = $this->leads->allOpenLeads();
            $allCompletedLeads = $this->leads->allCompletedLeads();
            $allInProcessLeads = $this->leads->allInProcessLeads();

            $Todayallleads = $this->leads->leadsToday();  
            $TodayallCompletedLeads = $this->leads->allCompletedLeadsToday();
            $TodayallInProcessLeads = $this->leads->allInProcessLeadsToday();

            $TodayallActivityLeads = $this->leads->allActivityToday(); 


        }
        // dd($Todayallleads,$TodayallOpenLeads,$TodayallCompletedLeads,$TodayallInProcessLeads);

        $totalPercentageLeads = $this->leads->percantageCompleted();
     /**
      * Statistics for today leads.
      *
      */
        $completedLeadsToday = $this->leads->completedLeadsToday();
        $createdLeadsToday = $this->leads->completedLeadsToday();

     /**
      * Statistics for leads this month.
      *
      */
        $leadCompletedThisMonth = $this->leads->completedLeadsThisMonth();

     /**
      * Statistics for leads each month(For Charts).
      *
      */
        $completedLeadsMonthly = $this->leads->createdLeadsMonthly();
        $createdLeadsMonthly = $this->leads->completedLeadsMonthly();

        
       
        return view('pages.dashboard', compact(
            'completedTasksToday',
            'completedLeadsToday',
            'createdTasksToday',
            'createdLeadsToday',
            'createdTasksMonthly',
            'completedTasksMonthly',
            'completedLeadsMonthly',
            'createdLeadsMonthly',
            'taskCompletedThisMonth',
            'leadCompletedThisMonth',
            'totalTimeSpent',
            'totalClients',
            'users',
            'companyname',
            'alltasks',
            'allCompletedTasks',
            'totalPercentageTasks',
            'allleads',
            'allCompletedLeads',
            'totalPercentageLeads',
            'allOpenLeads',
            'allInProcessLeads'            ,
            'Todayallleads'            , 
            'TodayallCompletedLeads'            ,
            'TodayallInProcessLeads',
            'TodayallActivityLeads'
        ));
    }
}
