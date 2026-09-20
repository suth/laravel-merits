<?php

namespace Suth\Merits\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Suth\Merits\Badge;

interface Badgeable
{
    /**
     * For querying and display only (e.g. eager loading, listing a recipient's
     * earned badges). Do not create/save/attach/delete on this relation directly —
     * doing so bypasses de-duplication, the trigger category, the rule that Manual
     * awards are never auto-revoked, and the BadgeAwarded/BadgeRevoked events.
     * Award or revoke badges via awardBadge()/BadgeService instead.
     */
    public function badges(): MorphMany;

    public function awardBadge(Badge $badge): void;

    public function hasBadge(Badge $badge): bool;
}
