<?php

namespace Suth\Merits\Contracts;

use Suth\Merits\Badge;
use Suth\Merits\Enums\TriggerCategory;

interface BadgeAwardRepository
{
    public function hasBadge(Badgeable $recipient, Badge $badge): bool;

    public function attach(Badgeable $recipient, Badge $badge, TriggerCategory $triggerCategory, array $meta = []): void;

    public function detach(Badgeable $recipient, Badge $badge): bool;
}
