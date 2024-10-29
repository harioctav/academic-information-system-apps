<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
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

  protected function mutateFormDataBeforeSave(array $data): array
  {
    $user = $this->record;

    $old = $user->profile_photo_path ?? null;
    $new = $data['profile_photo_path'] ?? null;

    if ($new !== null && $new !== $old) {
      if ($old && Storage::disk('public')->exists($old)) {
        Storage::disk('public')->delete($old);
      }
      $data['profile_photo_path'] = $new;
    } elseif ($new === null && $old !== null) {
      if (Storage::disk('public')->exists($old)) {
        Storage::disk('public')->delete($old);
      }
      $data['profile_photo_path'] = null;
    } else {
      $data['profile_photo_path'] = $old;
    }

    return $data;
  }

  protected function getSavedNotification(): ?Notification
  {
    return Notification::make()
      ->success()
      ->title(trans('notification.edit.title'))
      ->body(trans('notification.edit.body', ['label' => trans('pages-users::page.resource.label.user')]))
      ->send();
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl();
  }
}
