<?php

namespace App\Filament\Resources\MajorResource\Pages;

use App\Filament\Resources\MajorResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;
use Illuminate\Contracts\Support\Htmlable;

class ViewMajor extends ViewRecord
{
  protected static string $resource = MajorResource::class;

  public function getTitle(): string | Htmlable
  {
    $record = $this->getRecord();
    return $record->name;
  }

  protected function getHeaderActions(): array
  {
    return [
      Action::make('back')
        ->label(trans('button.back'))
        ->url(static::getResource()::getUrl())
        ->button()
        ->size(ActionSize::Small)
        ->icon(trans('button.back.icon'))
        ->iconSize('sm')
        ->color('secondary'),
    ];
  }
}
