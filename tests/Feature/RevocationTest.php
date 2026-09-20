<?php

use Illuminate\Support\Facades\Event;
use Suth\Merits\BadgeContext;
use Suth\Merits\BadgeService;
use Suth\Merits\Contracts\BadgeAwardRepository;
use Suth\Merits\Enums\TriggerCategory;
use Suth\Merits\Events\BadgeRevoked;
use Suth\Merits\Models\BadgeAward;
use Suth\Merits\Tests\Fixtures\Badges\AutoRevocableBadge;
use Suth\Merits\Tests\Fixtures\Badges\LenientAutoRevocableBadge;
use Suth\Merits\Tests\Fixtures\Badges\SimpleBadge;
use Suth\Merits\Tests\Fixtures\Models\Post;
use Suth\Merits\Tests\Fixtures\Models\User;

it('revokes an auto-revocable badge when it no longer qualifies', function () {
    Event::fake();
    $service = app(BadgeService::class);
    $badge = new AutoRevocableBadge;
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->for($user)->create();
    $context = BadgeContext::retroactive($user);

    $service->evaluate($badge, $context);
    expect($user->hasBadge($badge))->toBeTrue();

    $posts->each->delete();
    $service->evaluate($badge, $context);

    expect($user->hasBadge($badge))->toBeFalse();
    Event::assertDispatched(BadgeRevoked::class, function ($event) use ($badge, $context) {
        return $event->badge === $badge && $event->context === $context;
    });
});

it('does not revoke a manually awarded badge even when it no longer qualifies', function () {
    Event::fake();
    $service = app(BadgeService::class);
    $badge = new AutoRevocableBadge;
    $user = User::factory()->create();

    $service->manuallyAward($badge, $user);
    $context = BadgeContext::retroactive($user);

    $service->evaluate($badge, $context);

    expect($user->hasBadge($badge))->toBeTrue();
    Event::assertNotDispatched(BadgeRevoked::class);
});

it('does not revoke a non-auto-revocable badge when it no longer qualifies', function () {
    Event::fake();
    $service = app(BadgeService::class);
    $badge = new SimpleBadge;
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->for($user)->create();
    $context = BadgeContext::retroactive($user);

    $service->evaluate($badge, $context);
    expect($user->hasBadge($badge))->toBeTrue();

    $posts->each->delete();
    $service->evaluate($badge, $context);

    expect($user->hasBadge($badge))->toBeTrue();
    Event::assertNotDispatched(BadgeRevoked::class);
});

it('is a no-op evaluating an unawarded auto-revocable badge that does not qualify', function () {
    Event::fake();
    $service = app(BadgeService::class);
    $badge = new AutoRevocableBadge;
    $user = User::factory()->create();
    $context = BadgeContext::retroactive($user);

    $service->evaluate($badge, $context);

    expect($user->hasBadge($badge))->toBeFalse();
    Event::assertNotDispatched(BadgeRevoked::class);
});

it('honors a custom shouldRevoke() leeway before revoking', function () {
    Event::fake();
    $service = app(BadgeService::class);
    $badge = new LenientAutoRevocableBadge;
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->for($user)->create();
    $context = BadgeContext::retroactive($user);

    $service->evaluate($badge, $context);
    expect($user->hasBadge($badge))->toBeTrue();

    $posts->take(2)->each->delete();
    $service->evaluate($badge, $context);

    expect($user->hasBadge($badge))->toBeTrue();
    Event::assertNotDispatched(BadgeRevoked::class);

    $posts->last()->delete();
    $service->evaluate($badge, $context);

    expect($user->hasBadge($badge))->toBeFalse();
    Event::assertDispatched(BadgeRevoked::class);
});

it('fires the underlying Eloquent deleted event when a badge is revoked', function () {
    Event::fake(['eloquent.deleted: '.BadgeAward::class]);
    $service = app(BadgeService::class);
    $badge = new AutoRevocableBadge;
    $user = User::factory()->create();
    app(BadgeAwardRepository::class)->attach($user, $badge, TriggerCategory::EloquentEvent);
    $context = BadgeContext::retroactive($user);

    $service->evaluate($badge, $context);

    expect($user->hasBadge($badge))->toBeFalse();
    Event::assertDispatched('eloquent.deleted: '.BadgeAward::class);
});
