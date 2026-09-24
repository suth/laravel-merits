<?php

namespace Suth\Merits\Tests\Fixtures\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Contracts\ManuallyRegistered;

class DuplicateOfPostCountBadge extends Badge implements ManuallyRegistered
{
    public function key(): string
    {
        return 'post-count-badge';
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
