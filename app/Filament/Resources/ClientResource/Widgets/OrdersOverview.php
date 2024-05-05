<?php

namespace App\Filament\Resources\ClientResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class OrdersOverview extends BaseWidget
{
    public ?Model $record = null;

    protected int|string|array $columnSpan = '1/2';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Orders', Order::where('client_id', $this->record->id)->count()),
            Stat::make('Orders In Work', Order::where([
                'client_id' => $this->record->id,
                'status' => 'PROCESSING',
            ])->count()),
            Stat::make('Done Orders', Order::where([
                'client_id' => $this->record->id,
                'status' => 'DONE',
            ])->count()),
            Stat::make('Cancelled Orders', Order::where([
                'client_id' => $this->record->id,
                'status' => 'CANCELLED',
            ])->count()),
        ];
    }
}
