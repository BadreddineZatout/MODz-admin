<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\EmployeeResource\Widgets\EmployeeOverview;
use App\Filament\Resources\EmployeeResource\Widgets\OffersStatusOverview;
use App\Filament\Resources\EmployeeResource\Widgets\OrdersStatusOverview;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            EmployeeOverview::class,
            OrdersStatusOverview::class,
            OffersStatusOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Validate Employee')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->hidden(fn () => $this->record->status == 'VALID')
                ->action(function () {
                    $this->record->status = 'VALID';
                    $this->record->save();

                    return Notification::make()
                        ->title('Employee is Validated')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
            Action::make('Refuse Employee')
                ->color('danger')
                ->icon('heroicon-m-x-circle')
                ->hidden(fn () => $this->record->status == 'REFUSED')
                ->action(function () {
                    $this->record->status = 'REFUSED';
                    $this->record->save();

                    return Notification::make()
                        ->title('Employee is Refused')
                        ->danger()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
