<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool { return $user->activeCompany() !== null; }
    public function view(User $user, Client $client): bool { return $user->belongsToCompany($client->company) && ($user->isCompanyAdmin() || $user->isTeamLeader() || $client->user_id === $user->id); }
    public function create(User $user): bool { return $user->hasCompanyPermission('client-create'); }
    public function update(User $user, Client $client): bool { return $this->view($user, $client) && $user->hasCompanyPermission('client-update'); }
    public function delete(User $user, Client $client): bool { return $user->isCompanyAdmin(); }
    public function assign(User $user, Client $client): bool { return $user->isCompanyAdmin() || $user->isTeamLeader(); }
}
