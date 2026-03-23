<?php

namespace Suth\Merits\Contracts;

use Suth\Merits\Badge;

interface Badgeable
{
    public function awardBadge(Badge $badge): void;
    public function attachBadge(Badge $badge, string $triggerType, array $meta = []): void;
    public function hasBadge(Badge $badge): bool;
}
