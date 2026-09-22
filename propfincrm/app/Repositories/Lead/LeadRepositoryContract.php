<?php
namespace App\Repositories\Lead;

interface LeadRepositoryContract
{
    public function find($id);
    
    public function create($requestData);

    public function updateStatus($id, $requestData);

    public function updateFollowup($id, $requestData);

    public function updateAssign($id, $requestData);
    public function updateAssignDumpLead($id, $requestData);

    public function leads();
    public function allOpenLeads();
    public function allCompletedLeads();
    public function allInProcessLeads();

    public function leadsToday(); 
    public function allCompletedLeadsToday();
    public function allInProcessLeadsToday();
    public function allActivityToday();

    public function leadsByRole($user_id);
    public function allOpenLeadsByRole($user_id);
    public function allCompletedLeadsByRole($user_id);
    public function allInProcessLeadsByRole($user_id);

    public function leadsByRoleToday($user_id); 
    public function allCompletedLeadsByRoleToday($user_id);
    public function allInProcessLeadsByRoleToday($user_id);
    public function allActivityTodayByRole($user_id);

    public function percantageCompleted();

    public function completedLeadsToday();

    public function createdLeadsToday();

    public function completedLeadsThisMonth();

    public function createdLeadsMonthly();

    public function completedLeadsMonthly();

    public function totalOpenAndClosedLeads($id);
}
