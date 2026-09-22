<?php

namespace App\Support;

final class LeadStatus
{
    public const ACTIVE = 1;
    public const WON = 2;
    public const LOST = 5;
    public const ARCHIVED = -1;

    public static function label(int $status): string
    {
        return match ($status) {
            self::ACTIVE => 'Active',
            self::WON => 'Won',
            self::LOST => 'Lost',
            self::ARCHIVED => 'Archived',
            default => 'Unknown',
        };
    }

    public static function isTerminal(int $status): bool
    {
        return in_array($status, [self::WON, self::LOST, self::ARCHIVED], true);
    }
}
