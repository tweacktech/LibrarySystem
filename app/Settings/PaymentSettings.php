<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    public float $late_return_daily_rate;
    public float $lost_book_multiplier;

    public static function group(): string
    {
        return 'payment';
    }
}
