<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Suth\Merits\Contracts\BadgeRegistrationRepository;
use Suth\Merits\Models\BadgeRegistration;
use Suth\Merits\Tests\Fixtures\Badges\SimpleBadge;

it('creates a badge record on first registration', function () {
    $repository = app(BadgeRegistrationRepository::class);
    $badge = new SimpleBadge;

    $this->travelTo($now = Carbon::parse('2026-01-01 00:00:00'));
    $record = $repository->register($badge);

    expect($record->key)->toBe($badge->key())
        ->and($record->is_active)->toBeTrue()
        ->and($record->available_since)->toEqual($now);

    expect(BadgeRegistration::count())->toBe(1);
});

it('does not change available_since when re-registered after the cache is cleared', function () {
    $repository = app(BadgeRegistrationRepository::class);
    $badge = new SimpleBadge;

    $this->travelTo($originalTime = Carbon::parse('2026-01-01 00:00:00'));
    $repository->register($badge);

    Cache::flush();
    $this->travelTo(Carbon::parse('2026-06-01 00:00:00'));
    $record = $repository->register($badge);

    expect($record->available_since)->toEqual($originalTime);
});

it('does not create a duplicate record when registering the same badge twice', function () {
    $repository = app(BadgeRegistrationRepository::class);
    $badge = new SimpleBadge;

    $repository->register($badge);
    $repository->register($badge);

    expect(BadgeRegistration::count())->toBe(1);
});

it('does not create a duplicate record when re-registered after the cache is cleared', function () {
    $repository = app(BadgeRegistrationRepository::class);
    $badge = new SimpleBadge;

    $repository->register($badge);

    Cache::flush();
    $repository->register($badge);

    expect(BadgeRegistration::count())->toBe(1);
});

it('does not query the badge_registrations table on the second registration once cached', function () {
    $repository = app(BadgeRegistrationRepository::class);
    $badge = new SimpleBadge;

    $repository->register($badge);

    DB::enableQueryLog();
    $repository->register($badge);

    $table = (new BadgeRegistration)->getTable();
    $badgeRegistrationQueries = collect(DB::getQueryLog())
        ->filter(fn ($query) => str_contains($query['query'], $table));

    expect($badgeRegistrationQueries)->toBeEmpty();
});
