<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('state_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('province_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('profile.user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state.name'),
                Tables\Columns\TextColumn::make('province.name'),
                Tables\Columns\TextColumn::make('category.name'),
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
                //
            ])
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
            Infolists\Components\TextEntry::make('profile.user.email')
                ->copyable()
                ->copyMessage('Copied!')
                ->copyMessageDuration(1500),
            Infolists\Components\TextEntry::make('state.name'),
            Infolists\Components\TextEntry::make('province.name'),
            Infolists\Components\TextEntry::make('category.profession')
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
            Infolists\Components\Section::make('Validation Media')
                ->schema([
                    Infolists\Components\ImageEntry::make('Selfie')
                        ->state(function (Model $record) {
                            return $record->getSelfie();
                        })->size(500)
                        ->extraImgAttributes([
                            'alt' => 'Selfie image not found!',
                            'loading' => 'lazy',
                        ]),
                    Infolists\Components\ImageEntry::make('ID')
                        ->label('ID Card')
                        ->state(function (Model $record) {
                            return $record->getID();
                        })->size(500)
                        ->extraImgAttributes([
                            'alt' => 'ID card image not found!',
                            'loading' => 'lazy',
                        ]),
                ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'view' => Pages\ViewEmployee::route('/{record}'),
        ];
    }
}
