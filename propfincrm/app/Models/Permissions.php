<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Shanmuga\LaravelEntrust\Models\EntrustPermission;

class Permissions extends EntrustPermission
{
    use \App\Models\Concerns\UsesCentralConnection;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }
}
