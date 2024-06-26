<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Models\Problem;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProblemsRelationManager extends RelationManager
{
    protected static string $relationship = 'problems';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->numeric(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_date')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reporter'),
                Tables\Columns\IconColumn::make('is_treated')
                    ->boolean(),
            ])
            ->actions([
                Action::make('View')
                    ->url(fn (Problem $record): string => route('filament.admin.resources.problems.view', $record))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab(true),
            ])
            ->filters([
                Filter::make('report_date')
                    ->form([
                        DatePicker::make('report_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['report_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('report_date', $date),
                            );
                    }),
                SelectFilter::make('reporter')
                    ->options([
                        'CLIENT' => 'CLIENT',
                        'EMPLOYEE' => 'EMPLOYEE',
                    ]),
                SelectFilter::make('client')
                    ->relationship('client', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}"),
            ]);
    }
}
