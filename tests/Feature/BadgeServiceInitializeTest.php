<?php

use App\Badges\PostCountBadge;
use Illuminate\Support\Facades\Event;
use Suth\Merits\BadgeContext;
use Suth\Merits\BadgeService;
use Suth\Merits\Contracts\BadgeRegistrationRepository;
use Suth\Merits\Tests\Fixtures\Models\Post;
use Suth\Merits\Tests\Fixtures\Models\User;

beforeEach(function () {
    $this->app->useAppPath(__DIR__.'/../Fixtures/AppSkeleton');
});

it('registers every discovered badge with the registry', function () {
    $registrations = Mockery::mock(BadgeRegistrationRepository::class);
    $registrations->shouldReceive('register')->once()->with(Mockery::type(PostCountBadge::class));
    $this->app->instance(BadgeRegistrationRepository::class, $registrations);

    app(BadgeService::class)->initialize();
});

it('wires an Eloquent event listener that evaluates the badge when the model event fires', function () {
    $service = Mockery::mock(BadgeService::class, [app(BadgeRegistrationRepository::class)])->makePartial();
    $service->shouldReceive('evaluate')->andReturnNull();
    $this->app->instance(BadgeService::class, $service);

    app(BadgeService::class)->initialize();

    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $service->shouldHaveReceived('evaluate')->once()->with(
        Mockery::type(PostCountBadge::class),
        Mockery::on(fn (BadgeContext $context) => $context->trigger->is($post)),
    );
});

it('registers a listener only for the Eloquent event the badge declares', function () {
    app(BadgeService::class)->initialize();

    expect(Event::hasListeners('eloquent.created: '.Post::class))->toBeTrue()
        ->and(Event::hasListeners('eloquent.updated: '.Post::class))->toBeFalse()
        ->and(Event::hasListeners('eloquent.deleted: '.Post::class))->toBeFalse()
        ->and(Event::hasListeners('eloquent.saved: '.Post::class))->toBeFalse();
});

it('does not evaluate the badge for an Eloquent event the badge has not declared', function () {
    $service = Mockery::mock(BadgeService::class, [app(BadgeRegistrationRepository::class)])->makePartial();
    $service->shouldReceive('evaluate')->andReturnNull();
    $this->app->instance(BadgeService::class, $service);

    app(BadgeService::class)->initialize();

    $originalUser = User::factory()->create();
    $newUser = User::factory()->create();
    $post = Post::factory()->for($originalUser)->create();
    $post->user_id = $newUser->id;
    $post->save();

    $service->shouldHaveReceived('evaluate')->once();
});

it('automatically evaluates and awards a badge when a listened-to model event fires', function () {
    app(BadgeService::class)->initialize();

    $user = User::factory()->create();

    Post::factory()->for($user)->count(2)->create();
    expect($user->hasBadge(new PostCountBadge))->toBeFalse();

    Post::factory()->for($user)->create();

    expect($user->fresh()->hasBadge(new PostCountBadge))->toBeTrue();
});
