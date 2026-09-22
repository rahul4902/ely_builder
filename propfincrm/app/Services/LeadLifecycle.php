<?php

namespace App\Services;

use App\Models\Lead;
use App\Support\LeadStatus;
use DomainException;

class LeadLifecycle
{
    public function markWon(Lead $lead): void
    {
        $this->transition($lead, LeadStatus::WON);
    }

    public function applyFollowUpOutcome(Lead $lead, string $outcome): void
    {
        $normalized = strtolower(trim($outcome));
        $status = match ($normalized) {
            'closed lead', 'closed', 'won' => LeadStatus::WON,
            'number not valid', 'broker', 'not interested', 'lost' => LeadStatus::LOST,
            default => LeadStatus::ACTIVE,
        };

        $this->transition($lead, $status);
    }

    private function transition(Lead $lead, int $target): void
    {
        if ((int) $lead->status === $target) {
            return;
        }

        if (LeadStatus::isTerminal((int) $lead->status)) {
            throw new DomainException('A completed, lost, or archived lead must be explicitly reopened before its status can change.');
        }

        $lead->status = $target;
    }
}
