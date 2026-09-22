<?php

namespace App\Console\Commands;

use App\Services\ScheduledLeadAssignmentService;
use Illuminate\Console\Command;

class RunScheduledLeadAssignments extends Command
{
    protected $signature = 'leads:auto-assign';
    protected $description = 'Assign eligible leads within each active company scheduler.';

    public function handle(ScheduledLeadAssignmentService $assignments): int
    {
        $results = $assignments->assignAll();
        $this->info(array_sum($results) . ' lead(s) assigned across ' . count($results) . ' company(s).');

        return self::SUCCESS;
    }
}
