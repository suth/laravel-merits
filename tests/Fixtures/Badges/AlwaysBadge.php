<?php

namespace Suth\Merits\Tests\Fixtures\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;

class AlwaysBadge extends Badge
{
    public function key(): string
    {
        return 'always-badge';
    }

    public function qualify(BadgeContext $context): bool
    {
        return true;
    }

    public function resolveRecipient(object $trigger): ?Badgeable
    {
        return null;
    }
}
