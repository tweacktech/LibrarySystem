<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Library Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $slug = 'payments';

    public static function getNavigationLabel(): string
    {
        return 'Payments';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Payments';
    }

    public static function getModelLabel(): string
    {
        return 'Payment';
    }

    public static function getRouteBaseName(?string $panel = null): string
    {
        return 'admin.payments';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('book_id')
                    ->relationship('book', 'title')
                    ->nullable(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('₦'),
                Forms\Components\Select::make('payment_type')
                    ->options([
                        'late_return' => 'Late Return',
                        'lost_book' => 'Lost Book',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('payment_reference')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\KeyValue::make('payment_details')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_reference')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('book.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_type')
                    ->colors([
                        'warning' => 'late_return',
                        'danger' => 'lost_book',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_type')
                    ->options([
                        'late_return' => 'Late Return',
                        'lost_book' => 'Lost Book',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/')->name('index'),
            'create' => Pages\CreatePayment::route('/create')->name('create'),
            'view' => Pages\ViewPayment::route('/{record}')->name('view'),
            'edit' => Pages\EditPayment::route('/{record}/edit')->name('edit'),
        ];
    }
}
