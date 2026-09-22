<?php

namespace App\Listeners;

use App\Events\DumpLeadAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Activity;
use App\Models\DumpLead;
use Lang;

class DumpLeadActionLog
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
        switch ($event->getAction()) {
            case 'created':
                $text = __(':title was created by :creator and assigned to :assignee', [
                    'title' => $event->getLead()->title,
                    'creator' => $event->getLead()->creator->name,
                    'assignee' => $event->getLead()->user->name
                ]);
                break;
            case 'updated_status':
                $text = __('Lead was completed by :username', [
                    'username' => Auth()->user()->name,
                ]);
                break;
            case 'updated_deadline':
                $text = __(':username updated the deadline for this lead', [
                    'username' => Auth()->user()->name,
                ]);
                break;
            case 'updated_assign':
                $text = __(':username assigned lead to :assignee', [
                    'username' => Auth()->user()->name,
                    'assignee' => $event->getLead()->user->name
                ]);
                break;
            default:
                break;
        }

        $activityinput = array_merge(
            [
                'text' => $text,
                'user_id' => Auth()->id(),
                'source_type' => DumpLead::class,
                'source_id' =>  $event->getLead()->id,
                'action' => $event->getAction()
            ]
        );
        
        Activity::create($activityinput);
    }
}
