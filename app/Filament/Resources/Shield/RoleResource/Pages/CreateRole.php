<?php

namespace App\Filament\Resources\Shield\RoleResource\Pages;

use App\Filament\Resources\Shield\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CreateRole extends CreateRecord
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

  protected function mutateFormDataBeforeCreate(array $data): array
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

  protected function afterCreate(): void
  {
    $permissionModels = collect();
    $this->permissions->each(function ($permission) use ($permissionModels) {
      $permissionModels->push(Utils::getPermissionModel()::firstOrCreate([
        /** @phpstan-ignore-next-line */
        'name' => $permission,
        'guard_name' => $this->data['guard_name'],
      ]));
    });

    $this->record->syncPermissions($permissionModels);
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl();
  }
}
