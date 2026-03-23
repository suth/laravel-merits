<?php

namespace Suth\Merits\Tests\Fixtures\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;

class SimpleBadge extends Badge
{
    public function key(): string
    {
        return 'simple-badge';
    }

    public function qualify(BadgeContext $context): bool
    {
        return $context->recipient->posts()->count() >= 3;
    }

    public function resolveRecipient(object $trigger): ?Badgeable
    {
        return $trigger->user;
    }
}
