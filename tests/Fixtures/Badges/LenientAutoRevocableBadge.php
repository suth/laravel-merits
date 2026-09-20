<?php

namespace Suth\Merits\Tests\Fixtures\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\AutoRevocable;
use Suth\Merits\Contracts\Badgeable;

class LenientAutoRevocableBadge extends Badge implements AutoRevocable
{
    public function key(): string
    {
        return 'lenient-auto-revocable-badge';
    }

    public function qualify(BadgeContext $context): bool
    {
        return $context->recipient->posts()->count() >= 3;
    }

    public function shouldRevoke(BadgeContext $context): bool
    {
        return $context->recipient->posts()->count() === 0;
    }

    public function resolveRecipient(object $trigger): ?Badgeable
    {
        return $trigger->user;
    }
}
