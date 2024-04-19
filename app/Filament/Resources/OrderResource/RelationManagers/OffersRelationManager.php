<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OffersRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\TextEntry::make('employee.name'),
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
                Tables\Columns\TextColumn::make('employee.name')
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
            ->filters([
                Filter::make('Price')
                    ->form([
                        Forms\Components\TextInput::make('price_min')
                            ->suffix('DA')
                            ->numeric(),
                        Forms\Components\TextInput::make('price_max')
                            ->suffix('DA')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_min'],
                                fn (Builder $query, $price_min): Builder => $query->where('price', '>=', $price_min),
                            )
                            ->when(
                                $data['price_max'],
                                fn (Builder $query, $price_max): Builder => $query->where('price', '<=', $price_max),
                            );
                    }),
                SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pending',
                        'ACCEPTED' => 'Accepted',
                        'REFUSED' => 'Refused',
                    ]),
                TernaryFilter::make('can_travel'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
