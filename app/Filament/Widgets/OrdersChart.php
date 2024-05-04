<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Orders';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [
                        Order::where('status', 'PENDING')->count(),
                        Order::where('status', 'PROCESSING')->count(),
                        Order::where('status', 'DONE')->count(),
                        Order::where('status', 'CANCELLED')->count(),
                    ],
                    'backgroundColor' => ['gray', '#24C39E', 'yellow', 'red'],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['PENDING', 'PROCESSING', 'DONE', 'CANCELLED'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
