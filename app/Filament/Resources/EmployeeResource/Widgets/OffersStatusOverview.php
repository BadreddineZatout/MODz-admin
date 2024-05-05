<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Offer;
use Filament\Widgets\ChartWidget;

class OffersStatusOverview extends ChartWidget
{
    protected static ?string $heading = 'Offers';

    protected static ?string $maxHeight = '300px';

    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Offers',
                    'data' => [
                        Offer::where('status', 'PENDING')->count(),
                        Offer::where('status', 'ACCEPTED')->count(),
                        Offer::where('status', 'REFUSED')->count(),
                    ],
                    'backgroundColor' => ['gray', '#24C39E', 'red'],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['PENDING', 'ACCEPTED', 'REFUSED'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
