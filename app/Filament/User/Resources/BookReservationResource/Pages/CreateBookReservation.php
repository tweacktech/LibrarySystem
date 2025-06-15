<?php

namespace App\Filament\User\Resources\BookReservationResource\Pages;

use App\Filament\User\Resources\BookReservationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBookReservation extends CreateRecord
{
    protected static string $resource = BookReservationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
        $data['expires_at'] = now()->addDay();

        return $data;
    }
}
