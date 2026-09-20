<?php

namespace Suth\Merits\Contracts;

use Suth\Merits\Badge;
use Suth\Merits\Enums\TriggerCategory;

interface Badgeable
{
    public function awardBadge(Badge $badge): void;

    public function attachBadge(Badge $badge, TriggerCategory $triggerCategory, array $meta = []): void;

    public function hasBadge(Badge $badge): bool;
}
