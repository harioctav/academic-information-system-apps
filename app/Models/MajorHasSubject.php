<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MajorHasSubject extends Pivot
{
  use HasUuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'major_id',
    'subject_id',
    'semester',
  ];

  /**
   * Get the route key for the model.
   */
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  /**
   * Get the major associated with this subject.
   *
   * @return BelongsTo
   */
  public function major(): BelongsTo
  {
    return $this->belongsTo(Major::class);
  }

  /**
   * Get the subject associated with this major.
   *
   * @return BelongsTo
   */
  public function subject(): BelongsTo
  {
    return $this->belongsTo(Subject::class);
  }
}
