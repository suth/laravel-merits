<?php

use Illuminate\Support\Carbon;
use Suth\Merits\Models\BadgeRegistration;

it('uses the default badge_registrations table name', function () {
    // Default is 'badge_registrations'

    $tableName = (new BadgeRegistration)->getTable();

    expect($tableName)->toBe('badge_registrations');
});

it('uses a custom table name from config', function () {
    config()->set('merits.table_names.badge_registrations', 'custom_registrations');

    $tableName = (new BadgeRegistration)->getTable();

    expect($tableName)->toBe('custom_registrations');
});

it('casts is_active to a boolean', function () {
    $badgeRegistration = new BadgeRegistration(['is_active' => 1]);

    expect($badgeRegistration->is_active)->toBeTrue();
});

it('casts available_since to a datetime', function () {
    $badgeRegistration = new BadgeRegistration(['available_since' => '2026-01-01 00:00:00']);

    expect($badgeRegistration->available_since)->toBeInstanceOf(Carbon::class);
});

it('casts is_active and available_since even when a subclass overrides $casts', function () {
    $badgeRegistration = new class(['is_active' => 1, 'available_since' => '2026-01-01 00:00:00']) extends BadgeRegistration
    {
        protected $casts = ['some_other_column' => 'string'];
    };

    expect($badgeRegistration->is_active)->toBeTrue()
        ->and($badgeRegistration->available_since)->toBeInstanceOf(Carbon::class);
});
