<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallLog extends model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'call_logs';
    public $timestamps = false;
    protected $fillable = [
        'call_start_datetime',
        'call_end_datetime',
        'user_id',
        'created_at',
        'lead_id',
    ];
    protected $guarded = ['id'];
}
