<?php

return [

    'models' => [

        /*
         * This model stores metadata about the Badge such as created_at
         */

        'badge_record' => \Suth\Merits\Models\BadgeRecord::class,

        /*
         * This is the model that records when a user has a Badge
         */

        'badge_award' => \Suth\Merits\Models\BadgeAward::class,

    ],

    'table_names' => [

        /*
         * This is the table where badge awards are stored.
         */

        'badge_awards' => 'badge_awards',

    ],

];
