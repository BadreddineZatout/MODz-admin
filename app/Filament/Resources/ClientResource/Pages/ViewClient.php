<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Filament\Resources\ClientResource\Widgets\OrdersOverview;
use App\Filament\Resources\ClientResource\Widgets\OrdersStatusOverview;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            OrdersOverview::class,
            OrdersStatusOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Validate Client')
                ->label(function () {
                    if ($this->record->status == 'PENDING') {
                        return 'Validate';
                    }

                    return 'Unblock';
                })
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->hidden(fn () => $this->record->status == 'VALID')
                ->action(function () {
                    $this->record->status = 'VALID';
                    $this->record->save();

                    return Notification::make()
                        ->title(fn () => $this->record->status == 'PENDING' ? 'Client is Validated' : 'Client is Unblocked')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
            Action::make('Refuse Client')
                ->label(function () {
                    if ($this->record->status == 'PENDING') {
                        return 'Refuse';
                    }

                    return 'Block';
                })
                ->color('danger')
                ->icon('heroicon-m-x-circle')
                ->hidden(fn () => $this->record->status == 'REFUSED')
                ->action(function () {
                    $this->record->status = 'REFUSED';
                    $this->record->save();

                    return Notification::make()
                        ->title(fn () => $this->record->status == 'PENDING' ? 'Client is Refused' : 'Client is Blocked')
                        ->danger()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
