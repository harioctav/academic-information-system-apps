<?php

namespace App\Filament\Resources\Shield\RoleResource\Pages;

use App\Filament\Resources\Shield\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
  protected static string $resource = RoleResource::class;

  protected function getActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->icon(trans('button.create.icon'))
        ->iconSize('sm')
        ->size('sm')
        ->label(trans('button.create', ['label' => trans('filament-shield::filament-shield.resource.label.role')])),
    ];
  }
}
