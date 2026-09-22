<?php

namespace App\Events;

use App\Models\DumpLead;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DumpLeadAction
{
    private $lead;
    private $action;

    use InteractsWithSockets, SerializesModels;

    public function getLead()
    {
        return $this->lead;
    }
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Create a new event instance.
     * DumpLeadAction constructor.
     * @param DumpLead $lead
     * @param $action
     */
    public function __construct(DumpLead $lead, $action)
    {
        $this->lead = $lead;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
