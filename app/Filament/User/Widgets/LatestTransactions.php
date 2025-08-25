<?php

namespace App\Filament\User\Widgets;

use App\Enums\BorrowedStatus;
use App\Filament\User\Resources\TransactionResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTransactions extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(TransactionResource::getEloquentQuery())
            ->columns([
                Tables\Columns\TextColumn::make('book.title'),
                Tables\Columns\TextColumn::make('borrowed_at')
                    ->date(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (BorrowedStatus $state): string => match ($state) {
                        BorrowedStatus::Borrowed => 'warning',
                        BorrowedStatus::Returned => 'success',
                        BorrowedStatus::Delayed => 'danger',
                    }),
            ]);
    }
}
