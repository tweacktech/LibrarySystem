<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Library Settings
    |--------------------------------------------------------------------------
    |
    | This file contains all the settings for the library system
    |
    */

    // Daily rate for late returns (in naira)
    'late_return_daily_rate' => env('LATE_RETURN_DAILY_RATE', 100),

    // Default borrowing period in days
    'default_borrowing_period' => env('DEFAULT_BORROWING_PERIOD', 14),
];
