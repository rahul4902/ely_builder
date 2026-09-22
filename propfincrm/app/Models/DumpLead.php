<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use App\LeadsFollowUpTableModel;

class DumpLead extends Model
{
    use \App\Models\Concerns\BelongsToCompany;
    protected $table = 'dump_leads1';
    
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
        'user_created_id',
        'client_id',
        'contact_date',
        'lead_type',
        'company_id',
    ];
    protected $dates = ['contact_date'];

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
}
