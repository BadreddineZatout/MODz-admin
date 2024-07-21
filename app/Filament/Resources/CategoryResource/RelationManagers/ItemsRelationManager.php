<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->for_construction;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('unit')
                    ->maxLength(255),
                Forms\Components\TextInput::make('min_price')
                    ->suffix('DA')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('max_price')
                    ->suffix('DA')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('min_price')->suffix(' DA'),
                Tables\Columns\TextColumn::make('max_price')->suffix(' DA'),
            ])
            ->filters([
                Filter::make('Price')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->suffix('DA')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_price')
                            ->suffix('DA')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn (Builder $query, $min_price): Builder => $query->where('min_price', $min_price),
                            )
                            ->when(
                                $data['max_price'],
                                fn (Builder $query, $max_price): Builder => $query->where('max_price', $max_price),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
