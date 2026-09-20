<?php

namespace App\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;

class DuplicateBadgeFour extends Badge
{
    public function key(): string
    {
        return 'another-duplicate-badge';
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
