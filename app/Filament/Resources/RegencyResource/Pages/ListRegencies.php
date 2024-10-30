<?php

namespace App\Filament\Resources\RegencyResource\Pages;

use App\Filament\Resources\RegencyResource;
use App\Models\Province;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;

class ListRegencies extends ListRecords
{
  protected static string $resource = RegencyResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->mutateFormDataUsing(function (array $data): array {

          if (isset($data['province_id'])) {
            $province = Province::findOrFail($data['province_id']);
            $data['full_code'] = $province->code . $data['code'];
          }

          return $data;
        })
        ->icon(trans('button.create.icon'))
        ->size(ActionSize::Small)
        ->iconSize('sm')
        ->label(trans('button.create', ['label' => trans('pages-regencies::page.resource.label.regency')]))
        ->successNotification(
          Notification::make()
            ->success()
            ->title(trans('notification.create.title'))
            ->body(trans('notification.create.body', ['label' => trans('pages-regencies::page.resource.label.regency')])),
        ),
    ];
  }
}
