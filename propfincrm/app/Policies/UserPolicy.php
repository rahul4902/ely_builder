<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool { return $user->activeCompany() !== null; }
    public function view(User $user, User $subject): bool { return $user->isCompanyAdmin() || $user->belongsToActiveCompany($subject); }
    public function create(User $user): bool { return $user->isCompanyAdmin(); }
    public function update(User $user, User $subject): bool { return $user->isCompanyAdmin() || $user->id === $subject->id; }
    public function delete(User $user, User $subject): bool { return $user->isCompanyAdmin() && $user->id !== $subject->id; }
}
