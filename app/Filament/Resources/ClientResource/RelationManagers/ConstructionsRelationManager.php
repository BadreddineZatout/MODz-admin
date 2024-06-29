<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\Construction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConstructionsRelationManager extends RelationManager
{
    protected static string $relationship = 'constructions';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('categories.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jobType.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hour')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'gray',
                        'PROCESSING' => 'warning',
                        'DONE' => 'success',
                        'CANCELLED' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('accepted_at')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', $date),
                            );
                    }),
                SelectFilter::make('category')
                    ->relationship('categories', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('jobType')
                    ->relationship('jobType', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pending',
                        'PROCESSING' => 'Processing',
                        'DONE' => 'Done',
                        'CANCELLED' => 'Cancelled',
                    ]),
            ], layout: FiltersLayout::Modal)
            ->actions([
                Action::make('View')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->url(fn (Construction $record): string => route('filament.admin.resources.constructions.view', $record)),
            ]);
    }
}
