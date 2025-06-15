<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Library Settings
    |--------------------------------------------------------------------------
    |
    | This file contains all the configurable settings for the library system.
    |
    */

    'fine_per_day' => env('LIBRARY_FINE_PER_DAY', 10),

    // Daily rate for late returns (in naira)
    'late_return_daily_rate' => env('LATE_RETURN_DAILY_RATE', 100),

    // Multiplier for lost book charges
    'lost_book_multiplier' => env('LIBRARY_LOST_BOOK_MULTIPLIER', 100),

    // Default borrowing period in days
    'default_borrowing_period' => env('DEFAULT_BORROWING_PERIOD', 14),
];
