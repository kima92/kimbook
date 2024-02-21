<?php

namespace App\Filament\Resources;

use App\Enums\BookStatuses;
use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Filament\Resources\PatientResource\RelationManagers\BooksRelationManager;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('uuid')->searchable(),
                TextColumn::make('user.name'),
                IconColumn::make('status')
                    ->icon(fn (BookStatuses $state): string => match ($state) {
                        BookStatuses::Initial => 'heroicon-o-clock',
                        BookStatuses::GeneratingText => 'heroicon-o-pencil',
                        BookStatuses::GeneratingImages => 'heroicon-o-camera',
                        BookStatuses::FailedText, BookStatuses::FailedImages => 'heroicon-o-exclamation-triangle',
                        BookStatuses::Ready => 'heroicon-o-check-circle',
                        default =>  'heroicon-o-check-circle',
                    })
                    ->color(fn (BookStatuses $state): string => match ($state) {
                        BookStatuses::Initial => 'gray',
                        BookStatuses::GeneratingText, BookStatuses::GeneratingImages => 'info',
                        BookStatuses::FailedText, BookStatuses::FailedImages => 'heroicon-o-exclamation-triangle',
                        BookStatuses::Ready => 'success',
                        default =>  'heroicon-o-check-circle',
                    }),
                TextColumn::make('title'),
                TextColumn::make('costs_usd'),
                TextColumn::make('description'),
                TextColumn::make('input'),
                TextColumn::make('publication_date')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
