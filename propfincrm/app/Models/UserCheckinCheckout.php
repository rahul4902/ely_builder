<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCheckinCheckout extends model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_checkin_checkout';
    public $timestamps = false;
    protected $guarded = [];
}
