<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool { return $user->activeCompany() !== null; }
    public function view(User $user, Lead $lead): bool
    {
        return $user->belongsToCompany($lead->company) && ($user->isCompanyAdmin() || $user->isTeamLeader() || $lead->user_assigned_id === $user->id);
    }

    public function create(User $user): bool { return $user->hasCompanyPermission('lead-create'); }
    public function update(User $user, Lead $lead): bool { return $this->view($user, $lead) && $user->hasCompanyPermission('lead-update'); }
    public function delete(User $user, Lead $lead): bool { return $user->isCompanyAdmin() || ($this->view($user, $lead) && $user->hasCompanyPermission('lead-delete')); }
    public function assign(User $user, Lead $lead): bool { return $user->isCompanyAdmin() || $user->isTeamLeader(); }
    public function complete(User $user, Lead $lead): bool { return $user->isCompanyAdmin() || $lead->user_assigned_id === $user->id; }
}
