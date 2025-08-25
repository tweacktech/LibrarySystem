<?php

namespace App\Filament\User\Pages;

use App\Enums\BorrowedStatus;
use App\Models\Transaction;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TransactionHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string $view = 'filament.pages.transaction-history';

    protected static ?string $navigationLabel = 'My Transactions';

    protected static ?string $title = 'My Transactions';

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->where('user_id', Auth::id()))
            ->columns([
                TextColumn::make('book.title')
                    ->label('Book')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('borrowed_at')
                    ->label('Borrowed Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->label('Returned Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (BorrowedStatus $state): string => match ($state) {
                        BorrowedStatus::Borrowed => 'warning',
                        BorrowedStatus::Returned => 'success',
                        BorrowedStatus::Delayed => 'danger',
                    }),
            ]);
    }
}
