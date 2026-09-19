<?php

use Suth\Merits\Enums\TriggerType;
use Suth\Merits\Models\BadgeAward;
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

it('stores trigger_type as its backed string value', function () {
    $badgeAward = new BadgeAward(['trigger_type' => TriggerType::Manual]);

    expect($badgeAward->trigger_type)->toBe(TriggerType::Manual)
        ->and($badgeAward->getAttributes()['trigger_type'])->toBe('manual');
});

it('stores trigger_type as its backed string value even when a subclass overrides $casts', function () {
    $badgeAward = new class(['trigger_type' => TriggerType::Manual]) extends BadgeAward
    {
        protected $casts = ['some_other_column' => 'string'];
    };

    expect($badgeAward->trigger_type)->toBe(TriggerType::Manual)
        ->and($badgeAward->getAttributes()['trigger_type'])->toBe('manual');
});

it('reads a persisted trigger_type back as a TriggerType enum', function () {
    $user = User::factory()->create();

    $user->badges()->create([
        'badge_key' => 'simple-badge',
        'trigger_type' => TriggerType::Manual,
    ]);

    $badgeAward = BadgeAward::query()->first();

    expect($badgeAward->trigger_type)->toBe(TriggerType::Manual);
});
