<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackResource\Pages;
use App\Models\Pack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PackResource extends Resource
{
    protected static ?string $model = Pack::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                Forms\Components\Select::make('duration')
                    ->required()
                    ->options([
                        0 => 'Unlimited',
                        1 => '1 Month',
                        3 => '3 Month',
                        6 => '6 Month',
                        12 => '1 Year',
                    ])
                    ->default(1),
                Forms\Components\TextInput::make('order_limit')
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('DZD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_limit')
                    ->default('Unlimited')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            Infolists\Components\TextEntry::make('user.email'),
            Infolists\Components\TextEntry::make('pack.email'),
            Infolists\Components\TextEntry::make('starts_at')
                ->date('d-m-Y'),
            Infolists\Components\TextEntry::make('ends_at')
                ->date('d-m-Y'),
            Infolists\Components\TextEntry::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'PENDING' => 'gray',
                    'ACTIVE' => 'success',
                    'EXPIRED' => 'warning',
                    'CANCELLED' => 'danger',
                }),
            Infolists\Components\TextEntry::make('description')
                ->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePacks::route('/'),
        ];
    }
}
