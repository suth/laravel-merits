<?php

use Suth\Merits\BadgeDiscovery;
use Suth\Merits\Tests\Fixtures\Badges\AlwaysBadge;
use Suth\Merits\Tests\Fixtures\Badges\SimpleBadge;

it('discovers concrete badge classes within a directory', function () {
    $badges = BadgeDiscovery::within(
        __DIR__.'/../Fixtures/Badges',
        'Suth\\Merits\\Tests\\Fixtures\\Badges',
    );

    expect($badges->all())->toEqualCanonicalizing([
        SimpleBadge::class,
        AlwaysBadge::class,
    ]);
});

it('returns an empty collection for a directory that does not exist', function () {
    $badges = BadgeDiscovery::within('/path/that/does/not/exist', 'Whatever\\Namespace');

    expect($badges)->toBeEmpty();
});
