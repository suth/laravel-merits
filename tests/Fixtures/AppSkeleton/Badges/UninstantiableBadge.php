<?php

namespace App\Badges;

use RuntimeException;
use Suth\Merits\Badge;
use Suth\Merits\BadgeContext;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Contracts\ManuallyRegistered;

class UninstantiableBadge extends Badge implements ManuallyRegistered
{
    public function __construct()
    {
        throw new RuntimeException('UninstantiableBadge was instantiated but should have been excluded from discovery.');
    }

    public function key(): string
    {
        return 'uninstantiable-badge';
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
