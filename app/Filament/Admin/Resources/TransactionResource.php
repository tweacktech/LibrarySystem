<?php

namespace App\Filament\Admin\Resources;

use App\Enums\BorrowedStatus;
use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Http\Traits\NavigationCount;
use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TransactionResource extends Resource
{
    use NavigationCount;

    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Books & Transactions';

    protected static ?string $recordTitleAttribute = 'user.name';

    protected static ?int $globalSearchResultLimit = 20;

    /**
     * @param Transaction $record
     * @return array
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Borrower' => $record->user->name,
            'Book Borrowed' => $record->book->title,
            'Status' => $record->status,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Select::make('user_id')
                                            ->options(fn () => User::whereStatus(true)
                                                ->whereRelation('role', 'name', 'borrower')
                                                ->pluck('name', 'id'))
                                            ->native(false)
                                            ->searchable()
                                            ->preload()
                                            ->label('Borrower')
                                            ->required(),
                                        Select::make('book_id')
                                            ->options(fn () => Book::whereAvailable(true)
                                                ->pluck('title', 'id'))
                                            ->native(false)
                                            ->searchable()
                                            ->preload()
                                            ->label('Book')
                                            ->required(),
                                        DatePicker::make('borrowed_date')
                                            ->live()
                                            ->required(),
                                        TextInput::make('borrowed_for')
                                            ->suffix('Days')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(30)
                                            ->live()
                                            ->required(),
                                        DatePicker::make('returned_date')
                                            ->visible(fn (Get $get): bool => $get('status') === 'returned'
                                                || $get('status') === 'delayed')
                                            ->afterOrEqual('borrowed_date')
                                            ->live()
                                            ->required(fn (Get $get): bool => $get('status') === 'returned' || $get('status') === 'delayed')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ])->columnSpan(['sm' => 2, 'md' => 2, 'xxl' => 5]),
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        ToggleButtons::make('status')
                                            ->options(
                                                fn (string $operation) => $operation === 'create'
                                                ? [BorrowedStatus::Borrowed->value => BorrowedStatus::Borrowed->getLabel()]
                                                : BorrowedStatus::class
                                            )
                                            ->default(BorrowedStatus::Borrowed->value)
                                            ->required()
                                            ->inline()
                                            ->live(),
                                        Group::make()
                                            ->schema([
                                                Placeholder::make('fine')
                                                    ->label('$' . config('library.fine_per_day', 10) . ' Per Day After Delay')
                                                    ->content(
                                                        function (Get $get): string {
                                                            $borrowedDate = $get('borrowed_date');
                                                            $borrowedFor = $get('borrowed_for');
                                                            $returnedDate = $get('returned_date');
                                                            
                                                            if (!$borrowedDate || !$borrowedFor || !$returnedDate) {
                                                                return '0 Days x $' . config('library.fine_per_day', 10) . ' = $0.00';
                                                            }
                                                            
                                                            $borrowedDate = Carbon::parse($borrowedDate);
                                                            $returnedDate = Carbon::parse($returnedDate);
                                                            $dueDate = $borrowedDate->copy()->addDays($borrowedFor);
                                                            $delay = 0;
                                                            $fine = 0;
                                                            $finePerDay = config('library.fine_per_day', 10);
                                                            
                                                            if ($returnedDate->gt($dueDate)) {
                                                                $delay = $dueDate->diffInDays($returnedDate);
                                                                $fine = $delay * $finePerDay;
                                                            }

                                                            return $delay.' Days x $'.$finePerDay.' = $'.number_format($fine, 2);
                                                        }
                                                    )
                                                    ->live()
                                                    ->visible(fn (Get $get) => $get('returned_date')
                                                        && $get('status') === 'delayed'),
                                            ])->visibleOn('edit'),
                                    ]),
                            ])->columnSpan(['sm' => 2, 'md' => 1, 'xxl' => 1]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('book.title')
                    ->sortable()
                    ->searchable()
                    ->label('Borrowed Book'),
                TextColumn::make('borrowed_date')
                    ->date('d M, Y'),
                TextColumn::make('returned_date')
                    ->date('d M, Y'),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    Tables\Actions\Action::make('processLateReturn')
                        ->label('Process Late Return Payment')
                        ->icon('heroicon-o-banknotes')
                        ->color('warning')
                        ->visible(fn (Transaction $record): bool =>
                            $record->status === BorrowedStatus::Delayed &&
                            $record->fine > 0
                        )
                        ->action(function (Transaction $record) {
                            return $record->processLateReturn();
                        }),
                ]),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
