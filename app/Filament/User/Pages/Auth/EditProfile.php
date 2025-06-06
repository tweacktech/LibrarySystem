<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function mount(): void
    {
        parent::mount();
        $this->form->fill();
    }
} 