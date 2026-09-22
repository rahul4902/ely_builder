<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scheduler extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $fillable = [
        'from_date',
        'to_date',
        'time',
        'user_ids',
        'start_time',
        'end_time',
        'projects',
        'company_id',
    ];

    protected $casts = [
        'user_ids' => 'array',
        'projects' => 'array',
        'from_date' => 'date',
        'to_date' => 'date',
    ];
}
