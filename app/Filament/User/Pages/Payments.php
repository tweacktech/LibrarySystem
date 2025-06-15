<?php

namespace App\Filament\User\Pages;

use App\Models\Payment;
use App\Models\Role;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;

class Payments extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Payments';
    protected static ?string $title = 'My Payments';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.payments';

    public static function shouldRegister(): bool
    {
        return Auth::check() && Auth::user()->role?->name === Role::IS_BORROWER;
    }

    public function mount(): void
    {
        abort_unless(Auth::check() && Auth::user()->role?->name === Role::IS_BORROWER, 403);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->where('user_id', Auth::user()->id)
                    ->latest()
            )
            ->columns([
                TextColumn::make('payment_reference')
                    ->label('Reference')
                    ->searchable(),
                TextColumn::make('book.title')
                    ->label('Book')
                    ->searchable(),
                TextColumn::make('payment_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'late_return' => 'warning',
                        'lost_book' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
} 