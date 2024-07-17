<?php

namespace App\Filament\Resources\ConstructionResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->recordTitle(function ($record) {
                $profession = '';
                if ($record->type == 'INDIVIDUAL') {
                    $profession = $record->categories->first()->profession;
                }

                return "$record->first_name $record->last_name - $record->type".($profession ? " - $profession" : '');
            })
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('national_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state.name'),
                Tables\Columns\TextColumn::make('province.name'),
                Tables\Columns\TextColumn::make('categories.profession')
                    ->label('Profession'),
                Tables\Columns\TextColumn::make('type'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function (RelationManager $livewire, Builder $query) {
                        $query->where('can_work_construction', true)
                            ->whereHas(
                                'categories',
                                fn ($query) => $query
                                    ->whereIn('id', array_diff($livewire->getOwnerRecord()->categories->pluck('id')->toArray(), $livewire->getOwnerRecord()->assignedCategories()))
                            );
                        if ($livewire->getOwnerRecord()->type == 'INDIVIDUAL') {
                            $query->where('type', 'INDIVIDUAL');
                        }

                        return $query;
                    })->after(function (RelationManager $livewire, $record) {
                        if (! $livewire->getOwnerRecord()->type) {
                            if ($record->type == 'INDIVIDUAL') {
                                $livewire->getOwnerRecord()->type = 'INDIVIDUAL';
                            } else {
                                $livewire->getOwnerRecord()->type = 'GROUP';
                            }
                            $livewire->getOwnerRecord()->save();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->after(function (RelationManager $livewire, $record) {
                        if (! count($livewire->getOwnerRecord()->employees)) {
                            $livewire->getOwnerRecord()->type = null;
                            $livewire->getOwnerRecord()->save();
                        }
                    }),
            ]);
    }
}
