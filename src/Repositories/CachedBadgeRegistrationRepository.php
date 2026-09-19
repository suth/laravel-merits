<?php

namespace Suth\Merits\Repositories;

use Illuminate\Support\Facades\Cache;
use Suth\Merits\Badge;
use Suth\Merits\Contracts\BadgeRegistrationRepository;
use Suth\Merits\Models\BadgeRegistration;

class CachedBadgeRegistrationRepository implements BadgeRegistrationRepository
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
}
