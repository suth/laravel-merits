<?php

use Suth\Merits\Models\BadgeAward;
use Suth\Merits\Models\BadgeRecord;

return [

    'models' => [

        /*
         * This model stores metadata about the Badge such as created_at
         */

        'badge_record' => BadgeRecord::class,

        /*
         * This is the model that records when a user has a Badge
         */

        'badge_award' => BadgeAward::class,

    ],

    'table_names' => [

        /*
         * This is the table where badge awards are stored.
         */

        'badge_awards' => 'badge_awards',

    ],

];
