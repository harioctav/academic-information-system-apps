<?php

namespace App\Filament\Resources\MajorResource\Pages;

use App\Filament\Resources\MajorResource;
use App\Imports\Academics\MajorSubjectImport;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;

class ListMajors extends ListRecords
{
  protected static string $resource = MajorResource::class;

  protected function getHeaderActions(): array
  {
    return [
      \EightyNine\ExcelImport\ExcelImportAction::make()
        ->color("secondary")
        ->size(ActionSize::Small)
        ->iconSize('sm')
        ->slideOver()
        ->use(MajorSubjectImport::class),

      Actions\CreateAction::make()
        ->icon(trans('button.create.icon'))
        ->size(ActionSize::Small)
        ->iconSize('sm')
        ->label(trans('button.create', ['label' => trans('pages-majors::page.resource.label.major')]))
        ->successNotification(
          Notification::make()
            ->success()
            ->title(trans('notification.create.title'))
            ->body(trans('notification.create.body', ['label' => trans('pages-majors::page.resource.label.major')])),
        ),
    ];
  }
}
