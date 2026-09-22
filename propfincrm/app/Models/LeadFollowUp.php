<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFollowUp extends model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lead_follow_up';
    public $timestamps = false;

    protected $guarded = [];
}
