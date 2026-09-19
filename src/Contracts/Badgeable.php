<?php

namespace Suth\Merits\Contracts;

use Suth\Merits\Badge;
use Suth\Merits\Enums\TriggerType;

interface Badgeable
{
    public function awardBadge(Badge $badge): void;

    public function attachBadge(Badge $badge, TriggerType $triggerType, array $meta = []): void;

    public function hasBadge(Badge $badge): bool;
}
