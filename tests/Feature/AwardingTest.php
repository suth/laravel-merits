<?php

use Suth\Merits\BadgeContext;
use Suth\Merits\BadgeService;
use Suth\Merits\Tests\Fixtures\Badges\SimpleBadge;
use Suth\Merits\Tests\Fixtures\Models\Post;
use Suth\Merits\Tests\Fixtures\Models\User;
use Suth\Merits\Events\BadgeAwarded;
use Illuminate\Support\Facades\Event;

it('awards a badge when qualification is met', function () {
    Event::fake();
    $service = app(BadgeService::class);
    $badge = new SimpleBadge();
//    $service->register($badge);
    $user = User::factory()->create();
    Post::factory()->count(3)->for($user)->create();
    $context = BadgeContext::retroactive($user);

    $service->evaluate($badge, $context);

    expect($user->hasBadge($badge))->toBeTrue();
    Event::assertDispatched(BadgeAwarded::class, function ($event) use ($user, $context) {
        return $event->badge instanceof SimpleBadge
            && $event->context === $context;
    });
});

it('does not award a badge when qualification is not met', function () {
    Event::fake();
    $service = app(BadgeService::class);
    $badge = new SimpleBadge();
//    $service->register($badge);
    $user = User::factory()->create();
    Post::factory()->count(2)->for($user)->create();
    $context = BadgeContext::retroactive($user);

    $service->evaluate($badge, $context);

    expect($user->hasBadge($badge))->toBeFalse();
    Event::assertNotDispatched(BadgeAwarded::class);
});

it('does not duplicate an already awarded badge', function () {
    $service = app(BadgeService::class);
    $badge = new SimpleBadge();
//    $service->register($badge);
    $user = User::factory()->create();
    Post::factory()->count(3)->for($user)->create();
    $context = BadgeContext::retroactive($user);

    $service->evaluate($badge, $context);
    $service->evaluate($badge, $context);

    expect($user->badges()->where('badge_key', $badge->key())->count())->toBe(1);
});

it('manually awards a badge regardless of qualification', function () {
    $service = app(BadgeService::class);
    $badge = new SimpleBadge();
//    $service->register($badge);
    $user = User::factory()->create(); // No comments at all

    $service->award($badge, $user, manual: true);

    expect($user->hasBadge('simple-badge'))->toBeTrue()
        ->and($user->badges()->first()->manually_awarded)->toBeTrue();
});
