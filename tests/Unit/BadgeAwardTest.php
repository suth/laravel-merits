<?php

use Suth\Merits\Models\BadgeAward;

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
