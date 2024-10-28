<?php

namespace App\Enums;

use App\Traits\EnumsToArray;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel, HasColor
{
  use EnumsToArray;

  case SuperAdmin = 'super_admin';
  case SubjectRegisTeam = 'subject_regis_team';
  case FinanceTeam = 'finance_team';
  case StudentRegisTeam = 'student_regis_team';
  case FilingTeam = 'filing_team';

  public function getLabel(): string|null
  {
    return match ($this) {
      self::SuperAdmin => 'Super Admin',
      self::SubjectRegisTeam => 'Tim Regis Matakuliah',
      self::FinanceTeam => 'Tim Keuangan',
      self::StudentRegisTeam => 'Tim Pendaftaran Mahasiswa Baru',
      self::FilingTeam => 'Tim Pemberkasan',
    };
  }

  public function getColor(): array|string|null
  {
    return match ($this) {
      self::SuperAdmin => 'primary',
      self::SubjectRegisTeam => 'success',
      self::FinanceTeam => 'warning',
      self::StudentRegisTeam => 'secondary',
      self::FilingTeam => 'danger',
    };
  }
}
