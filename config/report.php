<?php

// use Carbon\Carbon;

return [

    // Set true if you like to collect user data on each visit. 
    'collect_visitor_data' => env('COLLECT_VISITOR_DATA', false),

    'sales' => [
        // Default reporting time in days
        'default' => 7,
        'take' => 10,
    ],

    // 'dafualt' => env('SCOUT_DRIVER', 'algolia'),

];
