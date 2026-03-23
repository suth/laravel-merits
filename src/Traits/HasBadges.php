<?php

namespace Suth\Merits\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Suth\Merits\Badge;

trait HasBadges
{
    public function badges(): MorphMany
    {
        return $this->morphMany(config('merits.models.badge_award'), 'badgeable');
    }

    public function awardBadge(Badge $badge): void
    {
        dd('error');
    }

    public function attachBadge(Badge $badge, string $triggerType, array $meta = []): void
    {
        $this->badges()->create([
            'badge_key' => $badge->key(),
            'trigger_type' => $triggerType,
        ]);
    }

    public function hasBadge(Badge $badge): bool
    {
        return $this->badges()->where('badge_key', $badge->key())->exists();
    }
}
