<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use \App\Models\Concerns\BelongsToCompany, \App\Models\Concerns\UsesCentralConnection;
    protected $fillable = [
        'task_complete_allowed',
        'task_assign_allowed',
        'lead_complete_allowed',
        'lead_assign_allowed',
        'company',
        'country',
        'company_id',
        'timezone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->belongsTo(Task::class);
    }
}
