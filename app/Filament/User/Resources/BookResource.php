<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Browse Books';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->disabled(),
                Forms\Components\TextInput::make('author.name')
                    ->label('Author')
                    ->disabled(),
                Forms\Components\TextInput::make('publisher.name')
                    ->label('Publisher')
                    ->disabled(),
                Forms\Components\TextInput::make('genre.name')
                    ->label('Genre')
                    ->disabled(),
                Forms\Components\TextInput::make('isbn')
                    ->disabled(),
                Forms\Components\TextInput::make('publication_year')
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('publisher.name')
                    ->label('Publisher')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('genre.name')
                    ->label('Genre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('isbn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('publication_year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'name'),
                Tables\Filters\SelectFilter::make('publisher')
                    ->relationship('publisher', 'name'),
                Tables\Filters\SelectFilter::make('genre')
                    ->relationship('genre', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'view' => Pages\ViewBook::route('/{record}'),
        ];
    }
} 