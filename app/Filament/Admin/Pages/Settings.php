<?php

namespace App\Filament\Admin\Pages;

use App\Settings\PaymentSettings;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class Settings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $title = 'Library Settings';
    protected static ?int $navigationSort = 100;

    protected static string $settings = PaymentSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Payment Settings')
                    ->description('Configure payment rates for late returns and lost books')
                    ->schema([
                        TextInput::make('late_return_daily_rate')
                            ->label('Late Return Daily Rate (₦)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->helperText('Amount to charge per day for delayed book returns'),
                        TextInput::make('lost_book_multiplier')
                            ->label('Lost Book Price Multiplier')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->step(0.1)
                            ->helperText('Multiply the book price by this factor for lost book charges'),
                    ])
            ]);
    }

    public function mount(): void
    {
        $this->form->fill([
            'late_return_daily_rate' => config('library.late_return_daily_rate', 100),
            'lost_book_multiplier' => config('library.lost_book_multiplier', 100),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save to PaymentSettings
        $settings = app(PaymentSettings::class);
        $settings->late_return_daily_rate = (float) $data['late_return_daily_rate'];
        $settings->lost_book_multiplier = (float) $data['lost_book_multiplier'];
        $settings->save();

        // Update config
        Config::set('library.late_return_daily_rate', $data['late_return_daily_rate']);
        Config::set('library.lost_book_multiplier', $data['lost_book_multiplier']);

        // Update .env file
        $this->updateEnvFile('LATE_RETURN_DAILY_RATE', $data['late_return_daily_rate']);
        $this->updateEnvFile('LIBRARY_LOST_BOOK_MULTIPLIER', $data['lost_book_multiplier']);

        // Show success notification using Filament's notification system
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Settings saved successfully!'
        ]);
    }

    protected function updateEnvFile(string $key, $value): void
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        if (str_contains($envContent, $key)) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            $envContent .= "\n{$key}={$value}";
        }

        File::put($envFile, $envContent);
    }
}
