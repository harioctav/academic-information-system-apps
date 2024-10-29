<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\AccountStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Ramsey\Uuid\Uuid as Generator;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
  use HasApiTokens;

  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory;
  use HasProfilePhoto;
  use Notifiable;
  use TwoFactorAuthenticatable;
  use HasRoles;

  public function canAccessPanel(Panel $panel): bool
  {
    return $this->status->value === AccountStatus::Active->value;
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($user) {
      $user->uuid = Generator::uuid4()->toString();
    });

    static::deleting(function ($user) {
      if ($user->profile_photo_path) {
        if (Storage::disk('public')->exists($user->profile_photo_path)) {
          Storage::disk('public')->delete($user->profile_photo_path);
        }
      }
    });
  }

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'name',
    'email',
    'password',
    'phone',
    'status',
    'profile_photo_path',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
    'two_factor_recovery_codes',
    'two_factor_secret',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array<int, string>
   */
  protected $appends = [
    'profile_photo_url',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'status' => AccountStatus::class
    ];
  }

  /**
   * Get the route key for the model.
   */
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  public function getFilamentAvatarUrl(): ?string
  {
    return $this->profile_photo_url;
  }
}
