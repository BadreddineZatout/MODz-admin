<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\OffersRelationManager;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                    ->preload()
                    ->searchable(['first_name', 'last_name'])
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->displayFormat('d/m/Y')
                    ->required(),
                Forms\Components\TimePicker::make('hour')
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\Select::make('job_type_id')
                    ->relationship('jobType', 'name')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'PENDING' => 'PENDING',
                        'PROCESSING' => 'PROCESSING',
                        'WAITING' => 'WAITING',
                        'DONE' => 'DONE',
                        'CANCELLED' => 'CANCELLED',
                    ])
                    ->hiddenOn('create')
                    ->required(),
                Forms\Components\Toggle::make('is_urgent')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('accepted_at')
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                    ->preload()
                    ->searchable(['first_name', 'last_name']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('date')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hour')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jobType.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'gray',
                        'PROCESSING' => 'warning',
                        'WAITING' => 'info',
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
                        'WAITING' => 'Waiting',
                        'DONE' => 'Done',
                        'CANCELLED' => 'Cancelled',
                    ]),
                TernaryFilter::make('is_urgent'),

            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(2)
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
            Infolists\Components\TextEntry::make('category.name'),
            Infolists\Components\TextEntry::make('jobType.name'),
            Infolists\Components\TextEntry::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'PENDING' => 'gray',
                    'PROCESSING' => 'warning',
                    'WAITING' => 'info',
                    'DONE' => 'success',
                    'CANCELLED' => 'danger',
                }),
            Infolists\Components\IconEntry::make('is_urgent')
                ->boolean(),
            Infolists\Components\TextEntry::make('accepted_at')
                ->date('d-m-Y'),
            Infolists\Components\TextEntry::make('employee.name')
                ->default('---'),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
