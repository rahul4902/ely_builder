<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    use \App\Models\Concerns\UsesCentralConnection;

    protected $table = "role_user";

    public function userRoleDetails()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
    
     public function getUserName()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
