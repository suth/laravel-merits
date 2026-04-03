<?php

use Suth\Merits\BadgeService;
use Suth\Merits\Tests\Fixtures\Badges\AlwaysBadge;
use Suth\Merits\Tests\Fixtures\Badges\SimpleBadge;
use Suth\Merits\Tests\Fixtures\Models\User;

it('badges() returns badge awards for the user', function () {
    $user = User::factory()->create();
    $badge = new SimpleBadge();
    $user->attachBadge($badge, 'manual');

    $badges = $user->badges;

    expect($badges)->toHaveCount(1)
        ->and($badges->first()->badge_key)->toBe($badge->key());
});

it('attachBadge() creates a badge award record', function () {
    $user = User::factory()->create();
    $badge = new SimpleBadge();

    $user->attachBadge($badge, 'manual');

    $this->assertDatabaseHas('badge_awards', [
        'badge_key' => $badge->key(),
        'trigger_type' => 'manual',
        'badgeable_type' => User::class,
        'badgeable_id' => $user->id,
    ]);
});

it('hasBadge() returns true when badge is awarded', function () {
    $user = User::factory()->create();
    $badge = new SimpleBadge();

    $user->attachBadge($badge, 'manual');

    $result = $user->hasBadge($badge);

    expect($result)->toBeTrue();
});

it('hasBadge() returns false when badge is not awarded', function () {
    $user = User::factory()->create();
    $badge = new SimpleBadge();

    $result = $user->hasBadge($badge);

    expect($result)->toBeFalse();
});

it('hasBadge() distinguishes between different badges', function () {
    $user = User::factory()->create();
    $simpleBadge = new SimpleBadge();
    $otherBadge = new AlwaysBadge();

    $user->attachBadge($simpleBadge, 'manual');

    $result = $user->hasBadge($otherBadge);

    expect($result)->toBeFalse();
});

it('awardBadge() delegates to BadgeService', function () {
    $user = User::factory()->create();
    $badge = new SimpleBadge();

    $service = Mockery::mock(BadgeService::class);
    $service->shouldReceive('manuallyAward')->once()->with($badge, $user);
    app()->instance(BadgeService::class, $service);

    $user->awardBadge($badge);
});
