<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Mokhosh\FilamentRating\Columns\RatingColumn;
use Mokhosh\FilamentRating\Entries\RatingEntry;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('client.name'),
                RatingColumn::make('score')
                    ->stars(5)
                    ->color('warning'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\TextEntry::make('client.name'),
            RatingEntry::make('score')
                ->label('Rating')
                ->stars(5)
                ->color('warning'),
            Infolists\Components\TextEntry::make('comment')
                ->columnSpanFull()
                ->default('---'),
        ]);
    }
}
