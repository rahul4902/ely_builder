<?php

namespace App\Models;

use App\Models\Concerns\UsesCentralConnection;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use UsesCentralConnection;

    protected $fillable = [
        'ticket_number',
        'company_id',
        'user_id',
        'subject',
        'category',
        'priority',
        'status',
        'last_reply_at',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'support_ticket_id')->orderBy('created_at', 'asc');
    }

    public function latestReply()
    {
        return $this->hasOne(TicketReply::class, 'support_ticket_id')->latestOfMany();
    }

    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $latest = static::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $latest ? ((int) substr($latest->ticket_number, -4)) + 1 : 1;

        return sprintf('TCK-%s-%04d', $year, $sequence);
    }

    public static function categories(): array
    {
        return [
            'technical' => 'Technical Support',
            'billing' => 'Billing & Invoicing',
            'feature_request' => 'Feature Request',
            'general' => 'General Inquiry',
        ];
    }

    public static function priorities(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    public static function statuses(): array
    {
        return [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];
    }
}
