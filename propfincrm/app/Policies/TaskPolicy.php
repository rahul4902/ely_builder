<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool { return $user->activeCompany() !== null; }
    public function view(User $user, Task $task): bool { return $user->belongsToCompany($task->company) && ($user->isCompanyAdmin() || $user->isTeamLeader() || $task->user_assigned_id === $user->id); }
    public function create(User $user): bool { return $user->hasCompanyPermission('task-create'); }
    public function update(User $user, Task $task): bool { return $this->view($user, $task); }
    public function assign(User $user, Task $task): bool { return $user->isCompanyAdmin() || $user->isTeamLeader(); }
    public function complete(User $user, Task $task): bool { return $user->isCompanyAdmin() || $task->user_assigned_id === $user->id; }
}
