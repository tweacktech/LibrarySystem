<?php

namespace App\Filament\Admin\Pages;

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Fine Settings')
                    ->description('Configure the fine amount for delayed book returns')
                    ->schema([
                        TextInput::make('fine_per_day')
                            ->label('Fine Per Day (₦)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->helperText('Amount to charge per day for delayed book returns')
                    ])
            ]);
    }

    public function mount(): void
    {
        $this->form->fill([
            'fine_per_day' => config('library.fine_per_day', 1),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Update config
        Config::set('library.fine_per_day', $data['fine_per_day']);
        
        // Update .env file
        $this->updateEnvFile('LIBRARY_FINE_PER_DAY', $data['fine_per_day']);
        
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
