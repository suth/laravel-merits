<?php

namespace Suth\Merits\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Suth\Merits\Badge;
use Suth\Merits\BadgeService;
use Suth\Merits\Contracts\BadgeAwardRepository;

trait HasBadges
{
    /**
     * For querying and display only (e.g. eager loading, listing a recipient's
     * earned badges). Avoid using create/save/attach/delete on this relation.
     * Award or revoke badges via awardBadge()/BadgeService instead.
     */
    public function badges(): MorphMany
    {
        return $this->morphMany(config('merits.models.badge_award'), 'badgeable');
    }

    public function awardBadge(Badge $badge): void
    {
        app(BadgeService::class)->manuallyAward($badge, $this);
    }

    public function hasBadge(Badge $badge): bool
    {
        return app(BadgeAwardRepository::class)->hasBadge($this, $badge);
    }
}
