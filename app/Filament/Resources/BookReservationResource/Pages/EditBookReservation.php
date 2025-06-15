<?php

namespace App\Filament\Resources\BookReservationResource\Pages;

use App\Filament\Resources\BookReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookReservation extends EditRecord
{
    protected static string $resource = BookReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
