<?php

namespace App\Filament\User\Widgets;

use App\Models\Book;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class AvailableBooksForReservation extends BaseWidget
{
    protected static ?string $heading = 'Available Books for Reservation';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Book::query()
                    ->where('available_copies', '>', 0)
                    ->whereDoesntHave('reservations', function (Builder $query) {
                        $query->where('status', 'pending')
                            ->where('expires_at', '>', now());
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_copies')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('reserve')
                    ->url(fn (Book $record): string => route('filament.user.resources.book-reservations.create', ['book_id' => $record->id]))
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->visible(fn (Book $record): bool => $record->available_copies > 0),
            ])
            ->paginated([10, 25, 50]);
    }
}
