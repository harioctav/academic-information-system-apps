<?php

namespace App\Filament\Resources\Shield\RoleResource\Pages;

use App\Filament\Resources\Shield\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EditRole extends EditRecord
{
  protected static string $resource = RoleResource::class;

  public Collection $permissions;

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

  protected function mutateFormDataBeforeSave(array $data): array
  {
    $this->permissions = collect($data)
      ->filter(function ($permission, $key) {
        return ! in_array($key, ['name', 'guard_name', 'select_all']);
      })
      ->values()
      ->flatten()
      ->unique();

    return Arr::only($data, ['name', 'guard_name']);
  }

  protected function afterSave(): void
  {
    $permissionModels = collect();
    $this->permissions->each(function ($permission) use ($permissionModels) {
      $permissionModels->push(Utils::getPermissionModel()::firstOrCreate([
        'name' => $permission,
        'guard_name' => $this->data['guard_name'],
      ]));
    });

    $this->record->syncPermissions($permissionModels);
  }
}
