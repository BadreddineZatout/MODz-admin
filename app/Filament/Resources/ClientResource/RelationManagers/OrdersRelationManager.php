<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hour')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jobType.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'gray',
                        'PROCESSING' => 'warning',
                        'DONE' => 'success',
                        'CANCELLED' => 'danger',
                    }),
                Tables\Columns\IconColumn::make('is_urgent')
                    ->boolean(),
                Tables\Columns\TextColumn::make('accepted_at')
                    ->date('d-m-Y')
                    ->sortable()
                    ->default('---')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->searchable(['first_name', 'last_name'])
                    ->default('---')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
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
                    ->relationship('category', 'name'),
                SelectFilter::make('jobType')
                    ->relationship('jobType', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pending',
                        'PROCESSING' => 'Processing',
                        'DONE' => 'Done',
                        'CANCELLED' => 'Cancelled',
                    ]),
                TernaryFilter::make('is_urgent'),

            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->actions([
                Action::make('View')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record)),
            ]);
    }
}
