<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConstructionResource\Pages;
use App\Filament\Resources\ConstructionResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\ConstructionResource\RelationManagers\OffersRelationManager;
use App\Models\Construction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ConstructionResource extends Resource
{
    protected static ?string $model = Construction::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'id')
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->required(),
                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'name')
                    ->preload()
                    ->multiple()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('hour')
                    ->required()
                    ->maxLength(191),
                Forms\Components\Select::make('type')
                    ->options([
                        'INDIVIDUAL' => 'INDIVIDUAL',
                        'GROUP' => 'GROUP',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'PENDING' => 'PENDING',
                        'PROCESSING' => 'PROCESSING',
                        'DONE' => 'DONE',
                        'CANCELLED' => 'CANCELLED',
                    ])
                    ->hiddenOn('create')
                    ->required(),
                Forms\Components\DatePicker::make('accepted_at'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hour')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type'),
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
                SelectFilter::make('type')
                    ->options([
                        'INDIVIDUAL' => 'Individual',
                        'GROUP' => 'Group',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pending',
                        'PROCESSING' => 'Processing',
                        'DONE' => 'Done',
                        'CANCELLED' => 'Cancelled',
                    ]),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\TextEntry::make('client.name'),
            Infolists\Components\TextEntry::make('date')
                ->date('d-m-Y'),
            Infolists\Components\TextEntry::make('hour'),
            Infolists\Components\TextEntry::make('categories.name'),
            Infolists\Components\TextEntry::make('type'),
            Infolists\Components\TextEntry::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'PENDING' => 'gray',
                    'PROCESSING' => 'warning',
                    'DONE' => 'success',
                    'CANCELLED' => 'danger',
                }),
            Infolists\Components\TextEntry::make('accepted_at')
                ->date('d-m-Y'),
            Infolists\Components\TextEntry::make('description')
                ->columnSpanFull(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            OffersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConstructions::route('/'),
            'create' => Pages\CreateConstruction::route('/create'),
            'view' => Pages\ViewConstruction::route('/{record}'),
            'edit' => Pages\EditConstruction::route('/{record}/edit'),
        ];
    }
}
