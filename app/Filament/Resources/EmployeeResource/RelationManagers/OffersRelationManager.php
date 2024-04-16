<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OffersRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\TextEntry::make('order.client.name')
                ->label('Client'),
            Infolists\Components\TextEntry::make('price'),
            Infolists\Components\IconEntry::make('can_travel')
                ->boolean(),
            Infolists\Components\TextEntry::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'PENDING' => 'gray',
                    'ACCEPTED' => 'success',
                    'REFUSED' => 'danger',
                }),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.client.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'gray',
                        'ACCEPTED' => 'success',
                        'REFUSED' => 'danger',
                    }),
                Tables\Columns\IconColumn::make('can_travel')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
