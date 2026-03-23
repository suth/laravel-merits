<?php

namespace Suth\Merits\Contracts;

use Suth\Merits\Badge;

interface Badgeable
{
    public function awardBadge(Badge $badge): void;
    public function attachBadge(Badge $badge): void;
    public function hasBadge(Badge $badge): bool;
}
