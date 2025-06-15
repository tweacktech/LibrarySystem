<?php

namespace App\Filament\User\Resources\BookReservationResource\Pages;

use App\Filament\User\Resources\BookReservationResource;
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