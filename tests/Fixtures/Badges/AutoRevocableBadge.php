<?php

namespace Suth\Merits\Tests\Fixtures\Badges;

use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\AutoRevocable;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Traits\RevokesImmediately;

class AutoRevocableBadge extends Badge implements AutoRevocable
{
    use RevokesImmediately;

    public function key(): string
    {
        return 'auto-revocable-badge';
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
