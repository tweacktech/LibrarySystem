<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        try {
            $this->migrator->add('payment.late_return_daily_rate', 100.00);
        } catch (\Spatie\LaravelSettings\Exceptions\SettingAlreadyExists $e) {
            // Setting already exists, we can ignore this
        }

        try {
            $this->migrator->add('payment.lost_book_multiplier', 2.00);
        } catch (\Spatie\LaravelSettings\Exceptions\SettingAlreadyExists $e) {
            // Setting already exists, we can ignore this
        }
    }

    public function down(): void
    {
        $this->migrator->delete('payment.late_return_daily_rate');
        $this->migrator->delete('payment.lost_book_multiplier');
    }
};
