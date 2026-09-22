<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use App\LeadsFollowUpTableModel;
use App\Support\LeadStatus;

class Lead extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $fillable = [
        'id',
        'title',
        'name',
        'contact_no',
        'email',
        'project',
        'source',
        'Budget',
        'country',
        'state',
        'city',
        'pin',
        'location',
        'requirement',
        'note',
        'status',
        'user_assigned_id',
        'user_assign_date',
        'user_created_id',
        'client_id',
        'contact_date',
        'lead_type',
        'lead_reverse',
        'lead_rev_date',
        'reverse_remark',
        'action_date',
        'company_id',
        'provider',
        'provider_lead_id',
        'external_payload',

    ];
    protected $dates = ['contact_date'];
    protected $casts = ['external_payload' => 'array'];

    protected $hidden = ['remember_token'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_assigned_id');
    }


    

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_created_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'lead_id', 'id');
    }

    public function getfollowup()
    {
        return $this->hasMany(LeadsFollowUpTableModel::class, 'lead_id', 'id')->orderBy('follow_up_date','desc');
    }

    public function latestFollowUp()
    {
        return $this->hasOne(LeadsFollowUpTableModel::class, 'lead_id', 'id')->latestOfMany('follow_up_date');
    }

    public function latestMeetingFollowUp()
    {
        return $this->hasOne(LeadsFollowUpTableModel::class, 'lead_id', 'id')
            ->whereNotNull('meeting_date')
            ->where('meeting_date', '!=', 'NULL')
            ->latestOfMany('meeting_date');
    }

    public function getfollowupActionDate($action_date)
    {  

        return $this->hasMany(LeadsFollowUpTableModel::class, 'lead_id', 'id')->whereDate('action_date',$action_date)->where('action_date','!=',null)->first();
    }

    public function activity()
    {
        return $this->morphMany(Activity::class, 'source');
    }

    public function getDaysUntilContactAttribute()
    {
        return Carbon\Carbon::now()->startOfDay()->diffInDays($this->contact_date, false);
    }

    public function followUps() {
        return $this->hasMany(LeadFollowUp::class, 'lead_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return LeadStatus::label((int) $this->status);
    }

    public function isActive(): bool
    {
        return (int) $this->status === LeadStatus::ACTIVE;
    }
}
