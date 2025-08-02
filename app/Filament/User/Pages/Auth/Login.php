<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('logo')
                    ->content(view('components.plasu-logo'))
                    ->columnSpanFull(),
                $this->getEmailFormComponent()->label('Email'),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    public function getHeading(): string
    {
        return 'User Login';
    }
} 