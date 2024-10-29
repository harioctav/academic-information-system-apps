<?php

namespace App\Filament\Resources\Shield\RoleResource\Pages;

use App\Filament\Resources\Shield\RoleResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;

class ViewRole extends ViewRecord
{
  protected static string $resource = RoleResource::class;

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
