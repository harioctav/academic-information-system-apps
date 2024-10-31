<?php

namespace App\Enums;

use App\Traits\EnumsToArray;
use Filament\Support\Contracts\HasLabel;

enum DegreeType: string implements HasLabel
{
  use EnumsToArray;

  case D3 = 'd3';
  case D4 = 'd4';
  case S1 = 's1';
  case S2 = 's2';

  public function getLabel(): string
  {
    return match ($this) {
      self::D3 => 'Diploma 3 (D3)',
      self::D4 => 'Diploma 4 (D4)',
      self::S1 => 'Sarjana (S1)',
      self::S2 => 'Magister (S2)',
    };
  }
}
