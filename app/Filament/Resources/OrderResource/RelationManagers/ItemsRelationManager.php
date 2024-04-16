<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('job_type_id')
                    ->relationship('jobType', 'name')
                    ->preload()
                    ->required(),
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
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('min_price')->suffix(' DA'),
                Tables\Columns\TextColumn::make('max_price')->suffix(' DA'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (RelationManager $livewire, Builder $query) => $query->where([
                        'category_id' => $livewire->getOwnerRecord()->category_id,
                        'job_type_id' => $livewire->getOwnerRecord()->job_type_id,
                    ]))
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('quantity')->numeric()->required(),
                    ])
                    ->color('primary'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
