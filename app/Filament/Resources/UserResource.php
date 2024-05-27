<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->hiddenOn('view'),
                Forms\Components\Select::make('role')
                    ->relationship('roles', 'name')
                    ->hidden(fn ($record) => $record && $record->current_role != 'ADMIN'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#'),
                Tables\Columns\TextColumn::make('name')
                    ->default('--')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_role')
                    ->default('--'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('current_role')
                    ->options([
                        'ADMIN' => 'Admin',
                        'CLIENT' => 'Client',
                        'EMPLOYEE' => 'Employee',
                    ]),
                TernaryFilter::make('profile')
                    ->label('Profiles')
                    ->placeholder('All users')
                    ->trueLabel('Users With Profiles')
                    ->falseLabel('Users Without Profiles')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('current_role'),
                        false: fn (Builder $query) => $query->whereNull('current_role'),
                        blank: fn (Builder $query) => $query,
                    ),
                Filter::make('admins')
                    ->query(fn (Builder $query): Builder => $query->where('current_role', 'ADMIN'))
                    ->toggle()
                    ->default(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
