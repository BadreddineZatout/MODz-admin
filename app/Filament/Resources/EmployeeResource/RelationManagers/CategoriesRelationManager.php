<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('profession')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('profession'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
