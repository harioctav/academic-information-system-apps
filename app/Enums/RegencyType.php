<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RegencyType: string implements Haslabel, HasColor, HasIcon
{
  case Regency = 'Kabupaten';
  case City = 'Kota';

  public function getLabel(): string|null
  {
    return match ($this) {
      self::Regency => 'Kabupaten',
      self::City => 'Kota',
    };
  }

  public function getColor(): array|string|null
  {
    return match ($this) {
      self::Regency => 'warning',
      self::City => 'success',
    };
  }

  public function getIcon(): string|null
  {
    return match ($this) {
      self::Regency => 'heroicon-o-building-office',
      self::City => 'heroicon-o-building-office-2',
    };
  }
}
