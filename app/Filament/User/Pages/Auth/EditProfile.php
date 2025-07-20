<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms\Components\View;

class EditProfile extends BaseEditProfile
{
    // public function form(Form $form): Form
    // {
    //     return $this->makeForm()
    //         ->schema([
    //             $this->getNameFormComponent(),
    //             $this->getEmailFormComponent(),
    //             $this->getPasswordFormComponent(),
    //             $this->getPasswordConfirmationFormComponent(),
    //             TextInput::make('department')
    //                 ->required()
    //                 ->maxLength(255),
    //             TextInput::make('id_number')
    //                 ->required()
    //                 ->unique('users', 'id_number', ignoreRecord: true)
    //                 ->maxLength(255),
    //             method_exists($this, 'getAddressFormComponent') ? $this->getAddressFormComponent() : null,
    //             method_exists($this, 'getPhoneFormComponent') ? $this->getPhoneFormComponent() : null,
    //         ])
    //         ->operation('edit')
    //         ->model($this->getUser())
    //         ->statePath('data');
    // }

    public function form(Form $form): Form
{
    $components = [
        $this->getNameFormComponent(),
        $this->getEmailFormComponent()->disabled(),
        $this->getPasswordFormComponent(),
        $this->getPasswordConfirmationFormComponent(),
        \Filament\Forms\Components\Select::make('department')
            ->required()
            ->options([
                'Science' => 'Science',
                'Arts' => 'Arts',
                'Commerce' => 'Commerce',
            ]),
        TextInput::make('id_number')
            ->required()
            ->unique('users', 'id_number', ignoreRecord: true)
            ->maxLength(255),
        method_exists($this, 'getAddressFormComponent') ? $this->getAddressFormComponent() : null,
        method_exists($this, 'getPhoneFormComponent') ? $this->getPhoneFormComponent() : null,
    ];

    // Remove any null values
    $components = array_filter($components);

    return $this->makeForm()
        ->schema($components)
        ->operation('edit')
        ->model($this->getUser())
        ->statePath('data');
}
    public function getHeaderActions(): array
    {
        return [
            \Filament\Pages\Actions\Action::make('back')
                ->label('Back')
                ->color('secondary')
                ->url(url()->previous()) // or route('home') for a fixed home page
                ->icon('heroicon-o-arrow-left'),
        ];
    }
    public function mount(): void
    {
        parent::mount();
    }
}
