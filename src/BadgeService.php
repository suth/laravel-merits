<?php

namespace Suth\Merits;

use Illuminate\Support\Facades\Cache;
use Suth\Merits\Contracts\Badgeable;
use Suth\Merits\Events\BadgeAwarded;
use Suth\Merits\Models\BadgeRegistration;

class BadgeService
{
    public function register(Badge $badge): BadgeRegistration
    {
        $records = Cache::rememberForever('merits.badge_registrations', fn () => BadgeRegistration::all()->keyBy('key'));

        if ($records->has($badge->key())) {
            return $records->get($badge->key());
        }

        $record = BadgeRegistration::firstOrCreate(
            ['key' => $badge->key()],
            ['is_active' => true, 'available_since' => now()],
        );

        Cache::forever('merits.badge_registrations', $records->put($badge->key(), $record));

        return $record;
    }

    public function evaluate(Badge $badge, BadgeContext $context): void
    {
        if ($badge->qualify($context)) {
            $this->award($badge, $context);
        }
    }

    public function award(Badge $badge, BadgeContext $context): void
    {
        if ($context->recipient->hasBadge($badge)) {
            return;
        }

        $context->recipient->attachBadge($badge, $context->triggerType(), $context->meta);
        BadgeAwarded::dispatch($badge, $context);
    }

    public function manuallyAward(Badge $badge, Badgeable $recipient): void
    {
        $context = BadgeContext::manual($recipient);
        $this->award($badge, $context);
    }
}
