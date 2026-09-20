<?php

namespace App\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;

class DuplicateBadgeOne extends Badge
{
    public function key(): string
    {
        return 'duplicate-badge';
    }

    public function qualify(BadgeContext $context): bool
    {
        return false;
    }

    public function resolveRecipient(object $trigger): ?Badgeable
    {
        return null;
    }
}
