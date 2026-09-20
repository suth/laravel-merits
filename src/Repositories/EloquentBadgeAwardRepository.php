<?php

namespace Suth\Merits\Repositories;

use Suth\Merits\Badge;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Contracts\BadgeAwardRepository;
use Suth\Merits\Enums\TriggerCategory;

class EloquentBadgeAwardRepository implements BadgeAwardRepository
{
    public function hasBadge(Badgeable $recipient, Badge $badge): bool
    {
        return $recipient->badges()->where('badge_key', $badge->key())->exists();
    }

    public function attach(Badgeable $recipient, Badge $badge, TriggerCategory $triggerCategory, array $meta = []): void
    {
        $recipient->badges()->create([
            'badge_key' => $badge->key(),
            'trigger_category' => $triggerCategory,
        ]);
    }

    public function detach(Badgeable $recipient, Badge $badge): bool
    {
        $awards = $recipient->badges()
            ->where('badge_key', $badge->key())
            ->where('trigger_category', '!=', TriggerCategory::Manual)
            ->get();

        if ($awards->isEmpty()) {
            return false;
        }

        $awards->each->delete();

        return true;
    }
}
