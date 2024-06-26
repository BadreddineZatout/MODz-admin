<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers\CategoriesRelationManager;
use App\Filament\Resources\EmployeeResource\RelationManagers\OffersRelationManager;
use App\Filament\Resources\EmployeeResource\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\EmployeeResource\RelationManagers\ProblemsRelationManager;
use App\Models\Employee;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?int $navigationSort = 2;

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return "$record->first_name $record->last_name";
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'gray',
                        'VALID' => 'success',
                        'REFUSED' => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->relationship('state', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('category')
                    ->relationship('categories', 'profession')
                    ->multiple(),
                TernaryFilter::make('is_active'),
                SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pending',
                        'VALID' => 'Valid',
                        'REFUSED' => 'Refused',
                    ]),
            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            Infolists\Components\TextEntry::make('first_name'),
            Infolists\Components\TextEntry::make('last_name'),
            Infolists\Components\TextEntry::make('phone')
                ->copyable()
                ->copyMessage('Copied!')
                ->copyMessageDuration(1500),
            Infolists\Components\TextEntry::make('national_id'),
            Infolists\Components\TextEntry::make('profile.user.email')
                ->copyable()
                ->copyMessage('Copied!')
                ->copyMessageDuration(1500),
            Infolists\Components\TextEntry::make('state.name'),
            Infolists\Components\TextEntry::make('province.name'),
            Infolists\Components\TextEntry::make('categories.profession')
                ->label('Profession'),
            Infolists\Components\IconEntry::make('is_active')
                ->boolean()
                ->label('Active State'),
            Infolists\Components\TextEntry::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'PENDING' => 'gray',
                    'VALID' => 'success',
                    'REFUSED' => 'danger',
                }),
            Infolists\Components\Section::make('Selfie Image')
                ->schema([
                    Infolists\Components\ImageEntry::make('Selfie')
                        ->state(function (Model $record) {
                            return $record->getSelfie();
                        })
                        ->extraImgAttributes([
                            'alt' => 'Selfie image not found!',
                            'loading' => 'lazy',
                        ]),

                ]),
            Infolists\Components\Section::make('National ID Images')
                ->schema([

                    Infolists\Components\ImageEntry::make('ID')
                        ->label('ID Card')
                        ->state(function (Model $record) {
                            return $record->getID();
                        })->height(300)->width(500)
                        ->extraImgAttributes([
                            'alt' => 'ID card image not found!',
                            'loading' => 'lazy',
                        ]),
                    Infolists\Components\ImageEntry::make('ID')
                        ->label('ID Card')
                        ->state(function (Model $record) {
                            return $record->getID(1);
                        })->height(300)->width(500)
                        ->extraImgAttributes([
                            'alt' => 'ID card image not found!',
                            'loading' => 'lazy',
                        ]),
                ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            OffersRelationManager::class,
            OrdersRelationManager::class,
            CategoriesRelationManager::class,
            ProblemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'view' => Pages\ViewEmployee::route('/{record}'),
        ];
    }
}
