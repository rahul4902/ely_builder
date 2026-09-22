<?php

namespace Tests\Unit;

use App\Models\Lead;
use App\Services\LeadLifecycle;
use App\Support\LeadStatus;
use DomainException;
use PHPUnit\Framework\TestCase;

class LeadLifecycleTest extends TestCase
{
    public function test_follow_up_outcomes_map_to_the_canonical_lifecycle(): void
    {
        $lifecycle = new LeadLifecycle();

        $active = new Lead(['status' => LeadStatus::ACTIVE]);
        $lifecycle->applyFollowUpOutcome($active, 'In Process Lead');
        $this->assertSame(LeadStatus::ACTIVE, $active->status);

        $won = new Lead(['status' => LeadStatus::ACTIVE]);
        $lifecycle->applyFollowUpOutcome($won, 'Closed Lead');
        $this->assertSame(LeadStatus::WON, $won->status);

        $lost = new Lead(['status' => LeadStatus::ACTIVE]);
        $lifecycle->applyFollowUpOutcome($lost, 'Not Interested');
        $this->assertSame(LeadStatus::LOST, $lost->status);
    }

    public function test_terminal_lead_cannot_be_changed_without_explicit_reopen(): void
    {
        $this->expectException(DomainException::class);

        $lead = new Lead(['status' => LeadStatus::WON]);
        (new LeadLifecycle())->applyFollowUpOutcome($lead, 'In Process Lead');
    }
}
