<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Models\JobType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JobTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'jobTypes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\Toggle::make('has_items')
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
                Tables\Columns\IconColumn::make('has_items')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('has_items'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->color('primary')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Action::make('View')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->url(fn (JobType $record): string => route('filament.admin.resources.job-types.view', $record)),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
