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
        'theme_mode',
        'theme_accent',
    ];

    protected $attributes = [
        'theme_mode' => 'light',
        'theme_accent' => '#0B57D0',
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
