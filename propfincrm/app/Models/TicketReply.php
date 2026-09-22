<?php

namespace App\Models;

use App\Models\Concerns\UsesCentralConnection;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use UsesCentralConnection;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'message',
        'is_superadmin_reply',
        'attachment_path',
    ];

    protected $casts = [
        'is_superadmin_reply' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
