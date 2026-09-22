<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Lead;
use App\Models\Scheduler;
use Illuminate\Support\Facades\DB;

class ScheduledLeadAssignmentService
{
    /** Assign currently eligible leads for every active company. */
    public function assignAll(): array
    {
        $results = [];
        foreach (Company::where('is_active', true)->get() as $company) {
            $results[$company->id] = $this->assignForCompany($company);
        }

        return $results;
    }

    /**
     * The explicit company predicates are intentional: this service is called
     * by CLI/scheduled processes, which do not have request company context.
     */
    public function assignForCompany(Company $company): int
    {
        $now = now();
        $assigned = 0;
        $schedulers = Scheduler::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->whereRaw("CONCAT(from_date, ' ', start_time) <= ?", [$now])
            ->whereRaw("CONCAT(to_date, ' ', end_time) >= ?", [$now])
            ->get();

        foreach ($schedulers as $scheduler) {
            $requestedUserIds = collect($scheduler->user_ids ?: [])->filter()->map(fn ($id) => (int) $id);
            $userIds = $company->activeUsers()
                ->wherePivotIn('role', ['company_admin', 'team_leader', 'employee'])
                ->whereIn('users.id', $requestedUserIds)
                ->pluck('users.id')
                ->all();

            if (!$userIds) {
                continue;
            }

            $assigned += DB::transaction(function () use ($company, $scheduler, $userIds) {
                $query = Lead::withoutGlobalScopes()
                    ->where('company_id', $company->id)
                    ->where(function ($query) {
                        $query->whereNull('user_assigned_id')
                            ->orWhere('user_assigned_id', 0)
                            ->orWhere('user_assigned_id', 1);
                    })
                    ->whereBetween('created_at', [
                        $scheduler->from_date->format('Y-m-d') . ' ' . $scheduler->start_time,
                        $scheduler->to_date->format('Y-m-d') . ' ' . $scheduler->end_time,
                    ]);

                if ($scheduler->projects) {
                    $query->whereIn('project', array_filter($scheduler->projects));
                }

                $leads = $query->lockForUpdate()->get();
                $index = (int) $scheduler->getAttribute('sec_index');
                $count = count($userIds);
                $assignedAt = now();

                foreach ($leads as $lead) {
                    $lead->update([
                        'user_assigned_id' => $userIds[$index % $count],
                        'user_assign_date' => $assignedAt,
                    ]);
                    $index++;
                }

                if ($leads->isNotEmpty()) {
                    $scheduler->setAttribute('sec_index', $index % $count);
                    $scheduler->save();
                }

                return $leads->count();
            });
        }

        return $assigned;
    }
}
