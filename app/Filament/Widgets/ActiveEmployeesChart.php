<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Widgets\ChartWidget;

class ActiveEmployeesChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Active Employees',
                    'data' => [Employee::active()->count(), Employee::inactive()->count()],
                    'backgroundColor' => ['#24C39E', 'red'],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Active Employees', 'Inactive Employees'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
