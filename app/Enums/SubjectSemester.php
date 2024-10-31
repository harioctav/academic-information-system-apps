<?php

namespace App\Enums;

use App\Traits\EnumsToArray;
use Filament\Support\Contracts\HasLabel;

enum SubjectSemester: int implements HasLabel
{
  use EnumsToArray;

  case ONE = 1;
  case TWO = 2;
  case THREE = 3;
  case FOUR = 4;
  case FIVE = 5;
  case SIX = 6;
  case SEVEN = 7;
  case EIGHT = 8;

  public function getLabel(): string|null
  {
    return match ($this) {
      self::ONE => "Semester 1",
      self::TWO => "Semester 2",
      self::THREE => "Semester 3",
      self::FOUR => "Semester 4",
      self::FIVE => "Semester 5",
      self::SIX => "Semester 6",
      self::SEVEN => "Semester 7",
      self::EIGHT => "Semester 8",
    };
  }
}
