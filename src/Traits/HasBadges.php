<?php

namespace Suth\Merits\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Suth\Merits\Badge;
use Suth\Merits\BadgeService;
use Suth\Merits\Enums\TriggerCategory;

trait HasBadges
{
    public function badges(): MorphMany
    {
        return $this->morphMany(config('merits.models.badge_award'), 'badgeable');
    }

    public function awardBadge(Badge $badge): void
    {
        app(BadgeService::class)->manuallyAward($badge, $this);
    }

    public function attachBadge(Badge $badge, TriggerCategory $triggerCategory, array $meta = []): void
    {
        $this->badges()->create([
            'badge_key' => $badge->key(),
            'trigger_category' => $triggerCategory,
        ]);
    }

    public function detachBadge(Badge $badge): bool
    {
        $awards = $this->badges()
            ->where('badge_key', $badge->key())
            ->where('trigger_category', '!=', TriggerCategory::Manual)
            ->get();

        if ($awards->isEmpty()) {
            return false;
        }

        $awards->each->delete();

        return true;
    }

    public function hasBadge(Badge $badge): bool
    {
        return $this->badges()->where('badge_key', $badge->key())->exists();
    }
}
