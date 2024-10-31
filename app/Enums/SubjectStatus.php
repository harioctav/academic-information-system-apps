<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SubjectStatus: string implements HasLabel, HasColor, HasIcon
{
  case Inti = 'I';
  case NonInti = 'N';

  public function getLabel(): string|null
  {
    return match ($this) {
      self::Inti => 'Inti',
      self::NonInti => 'Non Inti',
    };
  }

  public function getColor(): array|string|null
  {
    return match ($this) {
      self::Inti => 'success',
      self::NonInti => 'danger'
    };
  }

  public function getIcon(): string|null
  {
    return match ($this) {
      self::Inti => 'heroicon-o-check',
      self::NonInti => 'heroicon-o-x-mark'
    };
  }
}
