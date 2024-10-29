<?php

namespace App\Filament\Resources\ProvinceResource\Pages;

use App\Filament\Resources\ProvinceResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;

class ListProvinces extends ListRecords
{
  protected static string $resource = ProvinceResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->icon(trans('button.create.icon'))
        ->size(ActionSize::Small)
        ->iconSize('sm')
        ->label(trans('button.create', ['label' => trans('pages-provinces::page.resource.label.province')]))
        ->successNotification(
          Notification::make()
            ->success()
            ->title(trans('notification.create.title'))
            ->body(trans('notification.create.body', ['label' => trans('pages-provinces::page.resource.label.province')])),
        ),
    ];
  }
}
