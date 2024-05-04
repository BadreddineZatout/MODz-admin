<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            Stat::make('Clients Total', Client::count()),
            Stat::make('Employees Total', Employee::count()),
            Stat::make('Orders Total', Order::count()),
        ];
    }
}
