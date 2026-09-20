<?php

use Illuminate\Support\Facades\Event;
use Suth\Merits\Contracts\BadgeAwardRepository;
use Suth\Merits\Enums\TriggerCategory;
use Suth\Merits\Models\BadgeAward;
use Suth\Merits\Tests\Fixtures\Badges\AlwaysBadge;
use Suth\Merits\Tests\Fixtures\Badges\SimpleBadge;
use Suth\Merits\Tests\Fixtures\Models\User;

it('attach() creates a badge award record', function () {
    $repository = app(BadgeAwardRepository::class);
    $user = User::factory()->create();
    $badge = new SimpleBadge;

    $repository->attach($user, $badge, TriggerCategory::Manual);

    expect(BadgeAward::count())->toBe(1);
    $award = BadgeAward::first();
    expect($award->badgeable_type)->toBe(User::class)
        ->and($award->badgeable_id)->toBe($user->id)
        ->and($award->badge_key)->toBe($badge->key())
        ->and($award->trigger_category)->toBe(TriggerCategory::Manual);
});

it('hasBadge() returns true when badge is awarded', function () {
    $repository = app(BadgeAwardRepository::class);
    $user = User::factory()->create();
    $badge = new SimpleBadge;

    $repository->attach($user, $badge, TriggerCategory::Manual);

    expect($repository->hasBadge($user, $badge))->toBeTrue();
});

it('hasBadge() returns false when badge is not awarded', function () {
    $repository = app(BadgeAwardRepository::class);
    $user = User::factory()->create();
    $badge = new SimpleBadge;

    expect($repository->hasBadge($user, $badge))->toBeFalse();
});

it('hasBadge() distinguishes between different badges', function () {
    $repository = app(BadgeAwardRepository::class);
    $user = User::factory()->create();
    $simpleBadge = new SimpleBadge;
    $otherBadge = new AlwaysBadge;

    $repository->attach($user, $simpleBadge, TriggerCategory::Manual);

    expect($repository->hasBadge($user, $otherBadge))->toBeFalse();
});

it('detach() returns false when the badge was never awarded', function () {
    $repository = app(BadgeAwardRepository::class);
    $user = User::factory()->create();
    $badge = new SimpleBadge;

    expect($repository->detach($user, $badge))->toBeFalse();
});

it('detach() removes a non-manual award', function () {
    $repository = app(BadgeAwardRepository::class);
    $user = User::factory()->create();
    $badge = new SimpleBadge;
    $repository->attach($user, $badge, TriggerCategory::EloquentEvent);

    expect($repository->detach($user, $badge))->toBeTrue()
        ->and($repository->hasBadge($user, $badge))->toBeFalse();
});

it('detach() never removes a manually awarded badge', function () {
    $repository = app(BadgeAwardRepository::class);
    $user = User::factory()->create();
    $badge = new SimpleBadge;
    $repository->attach($user, $badge, TriggerCategory::Manual);

    expect($repository->detach($user, $badge))->toBeFalse()
        ->and($repository->hasBadge($user, $badge))->toBeTrue();
});

it('fires the underlying Eloquent deleted event when detaching', function () {
    Event::fake(['eloquent.deleted: '.BadgeAward::class]);
    $repository = app(BadgeAwardRepository::class);
    $user = User::factory()->create();
    $badge = new SimpleBadge;
    $repository->attach($user, $badge, TriggerCategory::EloquentEvent);

    $repository->detach($user, $badge);

    Event::assertDispatched('eloquent.deleted: '.BadgeAward::class);
});
