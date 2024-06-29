<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemResource\Pages;
use App\Models\Problem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProblemResource extends Resource
{
    protected static ?string $model = Problem::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->relationship('client', 'id')
                    ->required(),
                Forms\Components\Select::make('employee_id')
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->first_name} {$record->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->relationship('employee', 'id')
                    ->required(),
                Forms\Components\DatePicker::make('report_date')
                    ->required(),
                Forms\Components\Select::make('order_id')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->category->name} {$record->date}")
                    ->relationship('order', 'id'),
                //TODO: add Consturctions
                // Forms\Components\TextInput::make('construction_id')
                //     ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\SELECT::make('reporter')
                    ->options([
                        'CLIENT' => 'CLIENT',
                        'EMPLOYEE' => 'EMPLOYEE',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_treated'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->numeric(),
                Tables\Columns\TextColumn::make('client.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_date')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reporter'),
                Tables\Columns\IconColumn::make('is_treated')
                    ->boolean(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblems::route('/'),
            'create' => Pages\CreateProblem::route('/create'),
            'view' => Pages\ViewProblem::route('/{record}'),
            'edit' => Pages\EditProblem::route('/{record}/edit'),
        ];
    }
}
