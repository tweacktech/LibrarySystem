<?php

namespace App\Filament\User\Pages\Auth;

use App\Models\Role;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('logo')
                    ->content(view('components.plasu-logo'))
                    ->columnSpanFull(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                TextInput::make('department')
                    ->required()
                    ->maxLength(255),
                TextInput::make('id_number')
                    ->required()
                    ->unique('users', 'id_number')
                    ->maxLength(255),
            ])
            ->statePath('data');
    }

    public function getHeading(): string
    {
        return 'User Registration';
    }

    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => Role::getId(Role::IS_BORROWER),
            'department' => $data['department'],
            'id_number' => $data['id_number'],
            'email_verified_at' => now(), // Auto-verify email
        ]);

        event(new Registered($user));

        // Show notification
        Notification::make()
            ->title('Account created successfully!')
            ->body('You can now login with your credentials.')
            ->success()
            ->send();

        return $user;
    }

    // Redirect to login page after successful registration
    protected function getRedirectUrl(): ?string
    {
        return route('filament.user.auth.login');
    }
}
