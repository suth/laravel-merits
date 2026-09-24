<?php

use Suth\Merits\BadgeService;

it('falls back to app_path(\'Badges\') when merits.badges_path is not configured', function () {
    config(['merits.badges_path' => null]);

    expect(app(BadgeService::class)->badgesPath())->toBe(app_path('Badges'));
});

it('uses the configured badges_path when set', function () {
    config(['merits.badges_path' => '/custom/badges/path']);

    expect(app(BadgeService::class)->badgesPath())->toBe('/custom/badges/path');
});

it('derives the badge namespace from the app namespace and the path relative to app_path()', function () {
    $namespace = app(BadgeService::class)->badgesNamespace(app_path('Badges'));

    expect($namespace)->toBe('App\\Badges');
});

it('treats discovery as enabled by default when merits.discovery is not configured', function () {
    config(['merits.discovery' => null]);

    expect(app(BadgeService::class)->discoveryEnabled())->toBeTrue();
});

it('disables discovery when merits.discovery is explicitly set to false', function () {
    config(['merits.discovery' => false]);

    expect(app(BadgeService::class)->discoveryEnabled())->toBeFalse();
});
