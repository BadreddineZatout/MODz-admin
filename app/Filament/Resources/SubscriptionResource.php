<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subscription;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SubscriptionResource\Pages;

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
                    ->url(fn (Subscription $record): string => route('filament.admin.resources.employees.view', ['record' => $record->user->profile->employee->id]))
                    ->openUrlInNewTab()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pack.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'gray',
                        'ACTIVE' => 'success',
                        'CANCELLED' => 'danger',
                        'EXPIRED' => 'warning',
                    }),
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
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('success'),
                    Tables\Actions\EditAction::make()->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('Activate')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->visible(fn ($record) => $record->status == 'PENDING')
                        ->action(function ($record) {
                            Subscription::where([
                                'user_id' => $record->user_id,
                                'status' => 'ACTIVE',
                            ])->update([
                                'status' => 'CANCELLED',
                            ]);
                            $record->status = 'ACTIVE';
                            $record->starts_at = now();
                            $record->ends_at = now()->addMonths($record->pack->duration);
                            $record->save();

                            return Notification::make()
                                ->title('Employee subscription is activated')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                    Action::make('Cancel')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->visible(fn ($record) => $record->status != 'CANCELLED' || $record->status != 'EXPIRED')
                        ->action(function ($record) {
                            $record->status = 'CANCELLED';
                            $record->save();

                            return Notification::make()
                                ->title('Employee subscription is cancelled')
                                ->danger()
                                ->send();
                        })
                        ->requiresConfirmation(),
                ]),
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
