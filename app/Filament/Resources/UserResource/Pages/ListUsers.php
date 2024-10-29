<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;

class ListUsers extends ListRecords
{
  protected static string $resource = UserResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make()
        ->icon(trans('button.create.icon'))
        ->size(ActionSize::Small)
        ->iconSize('sm')
        ->label(trans('button.create', ['label' => trans('pages-users::page.resource.label.user')])),
    ];
  }
}
