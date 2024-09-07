<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers\CategoriesRelationManager;
use App\Filament\Resources\EmployeeResource\RelationManagers\OffersRelationManager;
use App\Filament\Resources\EmployeeResource\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\EmployeeResource\RelationManagers\ProblemsRelationManager;
use App\Filament\Resources\EmployeeResource\RelationManagers\RatingsRelationManager;
use App\Models\Employee;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
use Mokhosh\FilamentRating\Columns\RatingColumn;
use Mokhosh\FilamentRating\Entries\RatingEntry;

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
                    ->required()
                    ->tel(),
                Forms\Components\TextInput::make('national_id')
                    ->required(),
                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'name')
                    ->preload()
                    ->multiple()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'PENDING' => 'PENDING',
                        'VALID' => 'VALID',
                        'REFUSED' => 'DONE',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_active'),
                Forms\Components\Toggle::make('can_work_construction')
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            if ($get('type') === 'INDIVIDUAL' && $value) {
                                $fail('Only group employee can work constructions.');
                            }
                        },
                    ]),
                Forms\Components\Select::make('type')
                    ->options([
                        'INDIVIDUAL' => 'INDIVIDUAL',
                        'GROUP' => 'GROUP',
                    ])
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
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('national_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state.name'),
                Tables\Columns\TextColumn::make('province.name'),
                Tables\Columns\TextColumn::make('categories.profession')
                    ->label('Profession'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('profile.user.activeSubscription.pack.name')
                    ->label('Active Subscription')
                    ->default('---'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('can_work_construction')
                    ->toggleable()
                    ->boolean(),
                RatingColumn::make('rating')
                    ->stars(5)
                    ->color('warning'),
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
                TernaryFilter::make('can_work_construction'),
                SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pending',
                        'VALID' => 'Valid',
                        'REFUSED' => 'Refused',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'INDIVIDUAL' => 'Individual',
                        'GROUP' => 'Group',
                    ]),
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
            Infolists\Components\TextEntry::make('type'),
            Infolists\Components\TextEntry::make('profile.user.activeSubscription.pack.name')
                ->label('Active Subscription')
                ->default('---'),
            Infolists\Components\IconEntry::make('is_active')
                ->boolean()
                ->label('Active State'),
            Infolists\Components\IconEntry::make('can_work_construction')
                ->boolean()
                ->label('Can Work Construction'),
            Infolists\Components\TextEntry::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'PENDING' => 'gray',
                    'VALID' => 'success',
                    'REFUSED' => 'danger',
                }),
            RatingEntry::make('rating')
                ->stars(5)
                ->color('warning'),
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
            RatingsRelationManager::class,
            ProblemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
            'view' => Pages\ViewEmployee::route('/{record}'),
        ];
    }
}
