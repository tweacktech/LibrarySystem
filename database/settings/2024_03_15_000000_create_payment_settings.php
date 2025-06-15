<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Illuminate\Support\Facades\DB;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Check if settings exist in the database
        $settings = DB::table('settings')
            ->where('group', 'payment')
            ->pluck('name')
            ->toArray();

        if (!in_array('late_return_daily_rate', $settings)) {
            $this->migrator->add('payment.late_return_daily_rate', 100);
        }

        if (!in_array('lost_book_multiplier', $settings)) {
            $this->migrator->add('payment.lost_book_multiplier', 2);
        }
    }
};
