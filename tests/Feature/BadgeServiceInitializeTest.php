<?php

use App\Badges\PostCountBadge;
use App\Badges\WebhookBadge;
use Illuminate\Support\Facades\Event;
use Suth\Merits\BadgeContext;
use Suth\Merits\BadgeService;
use Suth\Merits\Contracts\BadgeAwardRepository;
use Suth\Merits\Contracts\BadgeRegistrationRepository;
use Suth\Merits\Exceptions\DuplicateBadgeKeysException;
use Suth\Merits\Tests\Fixtures\Badges\AlwaysBadge;
use Suth\Merits\Tests\Fixtures\Badges\DuplicateOfPostCountBadge;
use Suth\Merits\Tests\Fixtures\Badges\ParameterizedBadge;
use Suth\Merits\Tests\Fixtures\Events\FakeWebhookEvent;
use Suth\Merits\Tests\Fixtures\Models\Post;
use Suth\Merits\Tests\Fixtures\Models\User;
use Suth\Merits\Triggers\ManualTrigger;

beforeEach(function () {
    $this->app->useAppPath(__DIR__.'/../Fixtures/AppSkeleton');
});

it('registers every discovered badge with the registry', function () {
    $registrations = Mockery::mock(BadgeRegistrationRepository::class);
    $registrations->shouldReceive('register')->once()->with(Mockery::type(PostCountBadge::class));
    $registrations->shouldReceive('register')->once()->with(Mockery::type(WebhookBadge::class));
    $this->app->instance(BadgeRegistrationRepository::class, $registrations);
    $this->app->forgetInstance(BadgeService::class);

    app(BadgeService::class)->initialize();
});

it('throws with every duplicated key when badges declare colliding keys, without registering any of them', function () {
    $this->app->useAppPath(__DIR__.'/../Fixtures/DuplicateKeyAppSkeleton');
    $registrations = Mockery::mock(BadgeRegistrationRepository::class);
    $registrations->shouldNotReceive('register');
    $this->app->instance(BadgeRegistrationRepository::class, $registrations);
    $this->app->forgetInstance(BadgeService::class);

    try {
        app(BadgeService::class)->initialize();
        $this->fail('Expected DuplicateBadgeKeysException to be thrown.');
    } catch (DuplicateBadgeKeysException $exception) {
        expect($exception->getMessage())
            ->toContain('"duplicate-badge"')
            ->toContain('"another-duplicate-badge"')
            ->toContain('DuplicateBadgeOne')
            ->toContain('DuplicateBadgeTwo')
            ->toContain('DuplicateBadgeThree')
            ->toContain('DuplicateBadgeFour');
    }
});

it('wires an Eloquent event listener that evaluates the badge when the model event fires', function () {
    $service = Mockery::mock(BadgeService::class, [app(BadgeRegistrationRepository::class), app(BadgeAwardRepository::class)])->makePartial();
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
        ->and(Event::getRawListeners()['eloquent.created: '.Post::class])->toHaveCount(1)
        ->and(Event::hasListeners('eloquent.updated: '.Post::class))->toBeFalse()
        ->and(Event::hasListeners('eloquent.deleted: '.Post::class))->toBeFalse()
        ->and(Event::hasListeners('eloquent.saved: '.Post::class))->toBeFalse();
});

it('does not evaluate the badge for an Eloquent event the badge has not declared', function () {
    $service = Mockery::mock(BadgeService::class, [app(BadgeRegistrationRepository::class), app(BadgeAwardRepository::class)])->makePartial();
    $service->shouldReceive('evaluate')->andReturnNull();
    $this->app->instance(BadgeService::class, $service);
    app(BadgeService::class)->initialize();
    $originalUser = User::factory()->create();
    $newUser = User::factory()->create();

    $post = Post::factory()->for($originalUser)->create();
    $post->forceFill(['user_id' => $newUser->id])->save();

    $service->shouldHaveReceived('evaluate')->once();
});

it('automatically evaluates and awards a badge when a listened-to model event fires', function () {
    app(BadgeService::class)->initialize();
    $user = User::factory()->create();
    Post::factory()->for($user)->count(2)->create();

    Post::factory()->for($user)->create();

    expect($user->fresh()->hasBadge(new PostCountBadge))->toBeTrue();
});

it('wires a custom event listener that evaluates the badge when the event fires', function () {
    $service = Mockery::mock(BadgeService::class, [app(BadgeRegistrationRepository::class), app(BadgeAwardRepository::class)])->makePartial();
    $service->shouldReceive('evaluate')->andReturnNull();
    $this->app->instance(BadgeService::class, $service);
    app(BadgeService::class)->initialize();
    $user = User::factory()->create();
    $event = new FakeWebhookEvent($user);

    event($event);

    $service->shouldHaveReceived('evaluate')->once()->with(
        Mockery::type(WebhookBadge::class),
        Mockery::on(fn (BadgeContext $context) => $context->trigger === $event),
    );
});

it('registers a listener only for the custom event class the badge declares', function () {
    app(BadgeService::class)->initialize();

    expect(Event::hasListeners(FakeWebhookEvent::class))->toBeTrue()
        ->and(Event::getRawListeners()[FakeWebhookEvent::class])->toHaveCount(1)
        ->and(Event::hasListeners(Post::class))->toBeFalse();
});

it('does not evaluate the badge for an event it has not declared', function () {
    $service = Mockery::mock(BadgeService::class, [app(BadgeRegistrationRepository::class), app(BadgeAwardRepository::class)])->makePartial();
    $this->app->instance(BadgeService::class, $service);
    app(BadgeService::class)->initialize();

    event(new ManualTrigger);

    $service->shouldNotHaveReceived('evaluate');
});

it('automatically evaluates and awards a badge when a listened-to custom event fires', function () {
    app(BadgeService::class)->initialize();
    $user = User::factory()->create();

    event(new FakeWebhookEvent($user));

    expect($user->fresh()->hasBadge(new WebhookBadge))->toBeTrue();
});

it('does not attempt to instantiate a badge marked ManuallyRegistered during initialization', function () {
    expect(fn () => app(BadgeService::class)->initialize())->not->toThrow(RuntimeException::class);
});

it('registers a manually registered badge alongside discovered badges', function () {
    $registrations = Mockery::mock(BadgeRegistrationRepository::class);
    $registrations->shouldReceive('register')->once()->with(Mockery::type(PostCountBadge::class));
    $registrations->shouldReceive('register')->once()->with(Mockery::type(WebhookBadge::class));
    $registrations->shouldReceive('register')->once()->with(Mockery::type(AlwaysBadge::class));
    $this->app->instance(BadgeRegistrationRepository::class, $registrations);
    $this->app->forgetInstance(BadgeService::class);
    $service = app(BadgeService::class);
    $service->registerBadges([new AlwaysBadge]);

    $service->initialize();
});

it('throws a duplicate badge key exception when a manually registered badge collides with a discovered badge', function () {
    $service = app(BadgeService::class);
    $service->registerBadges([new DuplicateOfPostCountBadge]);

    try {
        $service->initialize();
        $this->fail('Expected DuplicateBadgeKeysException to be thrown.');
    } catch (DuplicateBadgeKeysException $exception) {
        expect($exception->getMessage())
            ->toContain('"post-count-badge"')
            ->toContain('PostCountBadge')
            ->toContain('DuplicateOfPostCountBadge');
    }
});

it('throws a duplicate badge key exception when two manually registered badges declare the same key', function () {
    $registrations = Mockery::mock(BadgeRegistrationRepository::class);
    $registrations->shouldNotReceive('register');
    $this->app->instance(BadgeRegistrationRepository::class, $registrations);
    $this->app->forgetInstance(BadgeService::class);
    $service = app(BadgeService::class);
    $service->registerBadges([new AlwaysBadge, new AlwaysBadge]);

    try {
        $service->initialize();
        $this->fail('Expected DuplicateBadgeKeysException to be thrown.');
    } catch (DuplicateBadgeKeysException $exception) {
        expect($exception->getMessage())
            ->toContain('"always-badge"')
            ->toContain('AlwaysBadge');
    }
});

it('evaluates and awards a manually registered parameterized badge when its declared event fires', function () {
    $service = app(BadgeService::class);
    $service->registerBadges([new ParameterizedBadge(3)]);
    $service->initialize();
    $user = User::factory()->create();
    Post::factory()->for($user)->count(2)->create();

    Post::factory()->for($user)->create();

    expect($user->fresh()->hasBadge(new ParameterizedBadge(3)))->toBeTrue();
});
