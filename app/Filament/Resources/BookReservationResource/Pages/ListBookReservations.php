<?php

namespace App\Filament\Resources\BookReservationResource\Pages;

use App\Filament\Resources\BookReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookReservations extends ListRecords
{
    protected static string $resource = BookReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
