<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Shanmuga\LaravelEntrust\Models\EntrustRole;
use App\Models\Permissions;

class Role extends EntrustRole
{
    use \App\Models\Concerns\UsesCentralConnection;

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    public function userRole()
    {
        return $this->hasMany(Role::class, 'user_id', 'id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'permission_role', 'role_id', 'permission_id');
    }
}
