<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Problem;
use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class EmployeeOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Orders', Order::where('employee_id', $this->record->id)->count()),
            Stat::make('Orders In Work', Order::where([
                'employee_id' => $this->record->id,
                'status' => 'PROCESSING',
            ])->count()),
            Stat::make('Done Orders', Order::where([
                'employee_id' => $this->record->id,
                'status' => 'DONE',
            ])->count()),
            Stat::make('Cancelled Orders', Order::where([
                'employee_id' => $this->record->id,
                'status' => 'CANCELLED',
            ])->count()),
            Stat::make('Offers', Offer::where('employee_id', $this->record->id)->count()),
            Stat::make('Accepted Offers', Offer::where([
                'employee_id' => $this->record->id,
                'status' => 'ACCEPTED',
            ])->count()),
            Stat::make('Refused Offers', Offer::where([
                'employee_id' => $this->record->id,
                'status' => 'REFUSED',
            ])->count()),
            Stat::make('Reported Problems', Problem::employeeReported()->where('employee_id', $this->record->id)->count()),
            Stat::make('Reported On Problems', Problem::clientReported()->where('employee_id', $this->record->id)->count()),
        ];
    }
}
