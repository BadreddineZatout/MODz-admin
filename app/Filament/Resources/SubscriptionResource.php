<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'email', function ($query) {
                        return $query->where('current_role', 'EMPLOYEE');
                    })
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->profile->employee->name)
                    ->required(),
                Forms\Components\Select::make('pack_id')
                    ->relationship('pack', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('starts_at')
                    ->hiddenOn(['create', 'edit']),
                Forms\Components\DatePicker::make('ends_at')
                    ->hiddenOn(['create', 'edit']),
                Forms\Components\Select::make('status')
                    ->options([
                        'PENDING' => 'PENDING',
                        'ACTIVE' => 'ACTIVE',
                        'EXPIRED' => 'EXPIRED',
                        'CANCELLED' => 'CANCELLED',
                    ])
                    ->hiddenOn(['create', 'edit'])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.profile.employee.name')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pack.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'email', function ($query) {
                        return $query->where('current_role', 'EMPLOYEE');
                    })
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->profile->employee->name)
                    ->label('User'),
                SelectFilter::make('pack_id')
                    ->relationship('pack', 'name')
                    ->label('Pack'),
                SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'PENDING',
                        'ACTIVE' => 'ACTIVE',
                        'EXPIRED' => 'EXPIRED',
                        'CANCELLED' => 'CANCELLED',
                    ]),
                Filter::make('start_date')
                    ->form([
                        DatePicker::make('start_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('starts_at', '>=', $date),
                            );
                    }),
                Filter::make('end_date')
                    ->form([
                        DatePicker::make('end_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('ends_at', '<=', $date),
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                //TODO: add activate and Cancel actions and groupe actions
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
