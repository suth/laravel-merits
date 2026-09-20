<?php

use Suth\Merits\Enums\TriggerCategory;
use Suth\Merits\Models\BadgeAward;
use Suth\Merits\Tests\Fixtures\Badges\SimpleBadge;
use Suth\Merits\Tests\Fixtures\Models\User;

it('uses the default badge_awards table name', function () {
    // Default is 'badge_awards'

    $tableName = (new BadgeAward)->getTable();

    expect($tableName)->toBe('badge_awards');
});

it('uses a custom table name from config', function () {
    config()->set('merits.table_names.badge_awards', 'custom_awards');

    $tableName = (new BadgeAward)->getTable();

    expect($tableName)->toBe('custom_awards');
});

it('stores trigger_category as its backed string value', function () {
    $badgeAward = new BadgeAward(['trigger_category' => TriggerCategory::Manual]);

    expect($badgeAward->trigger_category)->toBe(TriggerCategory::Manual)
        ->and($badgeAward->getAttributes()['trigger_category'])->toBe('manual');
});

it('stores trigger_category as its backed string value even when a subclass overrides $casts', function () {
    $badgeAward = new class(['trigger_category' => TriggerCategory::Manual]) extends BadgeAward
    {
        protected $casts = ['some_other_column' => 'string'];
    };

    expect($badgeAward->trigger_category)->toBe(TriggerCategory::Manual)
        ->and($badgeAward->getAttributes()['trigger_category'])->toBe('manual');
});

it('reads a persisted trigger_category back as a TriggerCategory enum', function () {
    $user = User::factory()->create();

    $user->awardBadge(new SimpleBadge);

    $badgeAward = BadgeAward::query()->first();
    expect($badgeAward->trigger_category)->toBe(TriggerCategory::Manual);
});
