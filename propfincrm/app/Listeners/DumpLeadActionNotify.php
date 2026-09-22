<?php

namespace App\Listeners;

use App\Events\DumpLeadAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\DumpLeadActionNotification;

class DumpLeadActionNotify
{
    /**
     * Action the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DumpLeadAction  $event
     * @return void
     */
    public function handle(DumpLeadAction $event)
    {
        $lead = $event->getLead();
        $action = $event->getAction();
        $lead->user->notify(new DumpLeadActionNotification(
            $lead,
            $action
        ));
    }
}
