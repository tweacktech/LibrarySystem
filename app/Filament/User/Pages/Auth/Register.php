<?php

namespace App\Filament\User\Pages\Auth;

use App\Models\Role;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => Role::getId(Role::IS_BORROWER),
            'department' => $data['department'],
            'id_number' => $data['id_number'],
        ]);

        event(new Registered($user));

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return route('filament.user.pages.dashboard');
    }
}
