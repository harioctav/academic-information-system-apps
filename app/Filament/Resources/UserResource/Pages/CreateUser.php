<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource;
use App\Models\Role;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
  protected static string $resource = UserResource::class;

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
    $data['password'] = Hash::make('password@123');
    return $data;
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl();
  }
}
