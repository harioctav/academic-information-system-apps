<?php

namespace App\Enums;

use App\Traits\EnumsToArray;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AccountStatus: int implements HasLabel, HasColor, HasIcon
{
  use EnumsToArray;

  case Active = 1;
  case InActive = 0;

  public function getLabel(): string|null
  {
    return match ($this) {
      self::Active => trans('enum.active'),
      self::InActive => trans('enum.inactive'),
    };
  }

  public function getColor(): array|string|null
  {
    return match ($this) {
      self::Active => 'success',
      self::InActive => 'danger',
    };
  }

  public function getIcon(): string|null
  {
    return match ($this) {
      self::Active => 'heroicon-o-check-circle',
      self::InActive => 'heroicon-o-x-mark',
    };
  }
}
