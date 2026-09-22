<?php

namespace App\Policies;

use App\Models\Integration;
use App\Models\User;

class IntegrationPolicy
{
    public function viewAny(User $user): bool { return $user->isCompanyAdmin(); }
    public function view(User $user, Integration $integration): bool { return $user->isCompanyAdmin($integration->company); }
    public function create(User $user): bool { return $user->isCompanyAdmin(); }
    public function update(User $user, Integration $integration): bool { return $this->view($user, $integration); }
    public function delete(User $user, Integration $integration): bool { return $this->view($user, $integration); }
}
