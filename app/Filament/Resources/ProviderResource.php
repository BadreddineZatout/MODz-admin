<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Filament\Resources\ProviderResource\RelationManagers\SocialMediaRelationManager;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $recordTitleAttribute = 'shop_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('shop_name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('phone_number2')
                    ->tel()
                    ->maxLength(191),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\Select::make('state_id')
                    ->relationship('state', 'name')
                    ->preload()
                    ->required()
                    ->live(),
                Forms\Components\Select::make('province_id')
                    ->relationship('province', 'name', fn (Builder $query, Get $get) => $query->where('state_id', $get('state_id')))
                    ->preload()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('images')
                    ->multiple()
                    ->disk(env('STORAGE_DISK'))
                    ->preserveFilenames()
                    ->rules(['image', 'mimes:jpeg,png,jpg']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shop_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number2')
                    ->searchable()
                    ->default('---'),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->relationship('state', 'name'),
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
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
            Infolists\Components\TextEntry::make('shop_name'),
            Infolists\Components\TextEntry::make('phone_number'),
            Infolists\Components\TextEntry::make('phone_number2')
                ->default('---'),
            Infolists\Components\TextEntry::make('category.name'),
            Infolists\Components\TextEntry::make('state.name'),
            Infolists\Components\TextEntry::make('province.name'),
            Infolists\Components\TextEntry::make('description')
                ->columnSpanFull(),
            SpatieMediaLibraryImageEntry::make('images'),

        ]);
    }

    public static function getRelations(): array
    {
        return [
            SocialMediaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'view' => Pages\ViewProvider::route('/{record}'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
