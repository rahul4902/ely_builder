<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Concerns\UsesCentralConnection;

class User extends Authenticatable
{
    use Notifiable, LaravelEntrustUserTrait, UsesCentralConnection;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'address', 'personal_number', 'work_number', 'image_path'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $dates = ['trial_ends_at', 'subscription_ends_at'];
    protected $hidden = ['password', 'password_confirmation', 'remember_token'];


    protected $primaryKey = 'id';

    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_assigned_id', 'id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'user_id', 'id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot(['role', 'teamlead_user_id', 'is_active'])
            ->withTimestamps();
    }

    public function activeCompany()
    {
        return app(\App\Support\CurrentCompany::class)->get();
    }

    public function belongsToCompany(Company $company): bool
    {
        return $this->companies()->whereKey($company->id)->wherePivot('is_active', true)->exists();
    }

    public function belongsToActiveCompany(User $user): bool
    {
        $company = $this->activeCompany();
        return $company && $user->companies()->whereKey($company->id)->wherePivot('is_active', true)->exists();
    }

    public function companyRole(?Company $company = null): ?string
    {
        $company = $company ?: $this->activeCompany();
        if (!$company) {
            return null;
        }
        return $this->companies()->whereKey($company->id)->wherePivot('is_active', true)->first()?->pivot->role;
    }

    public function isCompanyAdmin(?Company $company = null): bool
    {
        if ($this->hasRole('administrator') || $this->hasRole('super_administrator')) {
            return true;
        }
        return $this->companyRole($company) === 'company_admin';
    }

    public function isPlatformSuperAdmin(): bool
    {
        return $this->hasRole('super_administrator');
    }

    public function isTeamLeader(?Company $company = null): bool
    {
        return in_array($this->companyRole($company), ['company_admin', 'team_leader'], true);
    }

    public function hasCompanyPermission(string $permission): bool
    {
        if ($this->hasRole('administrator') || $this->hasRole('super_administrator')) {
            return true;
        }

        $role = $this->companyRole();
        if (!$role) {
            return $this->hasPermission($permission);
        }

        // company_user determines which existing Entrust role applies in the
        // active company. The legacy role/permission tables remain the only
        // permission source; no duplicate company role table is used.
        $legacyRole = match ($role) {
            'company_admin' => 'administrator',
            'team_leader' => 'team leader',
            default => $role,
        };

        if ($legacyRole === 'administrator') {
            return true;
        }

        return Role::where('name', $legacyRole)
            ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
            ->exists();
    }

    /**
     * Bridge Laravel's Authorizable/Gate contract with Entrust's can() implementation.
     *
     * @param string|array $ability
     * @param mixed $arguments
     * @return bool
     */
    public function can($ability, $arguments = [])
    {
        if (!empty($arguments)) {
            return \Illuminate\Support\Facades\Gate::forUser($this)->check($ability, $arguments);
        }

        if (\Illuminate\Support\Facades\Gate::forUser($this)->has($ability)) {
            return \Illuminate\Support\Facades\Gate::forUser($this)->check($ability);
        }

        if ($this->hasRole('administrator') || $this->hasRole('super_administrator')) {
            return true;
        }

        if (is_string($ability) && $this->hasCompanyPermission($ability)) {
            return true;
        }

        return $this->hasPermission($ability);
    }
    
    public function department()
    {
        return $this->belongsToMany(Department::class, 'department_user')->withPivot('department_id');
    }


    public function teamlead()
    {
        return $this->belongsToMany(User::class, 'id')->withPivot('user_id');
    }

    public function userRole()
    {
        return $this->hasOne(RoleUser::class, 'user_id', 'id');
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    public function getLeader()
    {
        return $this->belongsTo(User::class, 'teamlead');
    }

    public function getNameAndDepartmentAttribute()
    {
        return $this->name . ' ' . '(' . $this->department()->first()->name . ')';
    }
    
    public function moveTasks($user_id)
    {
        $tasks = $this->tasks()->get();
        foreach ($tasks as $task) {
            $task->user_assigned_id = $user_id;
            $task->save();
        }
    }

    public function moveLeads($user_id)
    {
        $leads = $this->leads()->get();
        foreach ($leads as $lead) {
            $lead->user_assigned_id = $user_id;
            $lead->save();
        }
    }

    public function moveClients($user_id)
    {
        $clients = $this->clients()->get();
        foreach ($clients as $client) {
            $client->user_id = $user_id;
            $client->save();
        }
    }
}
