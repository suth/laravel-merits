<?php

use App\Badges\PostCountBadge;
use App\Badges\WebhookBadge;
use Illuminate\Support\Facades\Event;
use Suth\Merits\BadgeService;
use Suth\Merits\Contracts\BadgeRegistrationRepository;
use Suth\Merits\Tests\Fixtures\Models\Post;

beforeEach(function () {
    $this->app->useAppPath(__DIR__.'/../Fixtures/AppSkeleton');
});

it('skips filesystem discovery entirely when merits.discovery is set to false', function () {
    config(['merits.discovery' => false]);
    $registrations = Mockery::mock(BadgeRegistrationRepository::class);
    $registrations->shouldNotReceive('register');
    $this->app->instance(BadgeRegistrationRepository::class, $registrations);
    $this->app->forgetInstance(BadgeService::class);

    app(BadgeService::class)->initialize();

    expect(Event::hasListeners('eloquent.created: '.Post::class))->toBeFalse();
});

it('performs filesystem discovery by default when merits.discovery is not set to false', function () {
    $registrations = Mockery::mock(BadgeRegistrationRepository::class);
    $registrations->shouldReceive('register')->once()->with(Mockery::type(PostCountBadge::class));
    $registrations->shouldReceive('register')->once()->with(Mockery::type(WebhookBadge::class));
    $this->app->instance(BadgeRegistrationRepository::class, $registrations);
    $this->app->forgetInstance(BadgeService::class);

    app(BadgeService::class)->initialize();
});
