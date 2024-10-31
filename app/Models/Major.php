<?php

namespace App\Models;

use App\Enums\DegreeType;
use App\Enums\SubjectNote;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Major extends Model
{
  use HasUuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'code',
    'name',
    'degree',
    'total_course_credit',
  ];

  /**
   * Get the route key for the model.
   */
  public function getRouteKeyName(): string
  {
    return 'uuid';
  }

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'degree' => DegreeType::class
    ];
  }

  /**
   * Get the subjects associated with this major.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function subjects(): BelongsToMany
  {
    return $this->belongsToMany(
      Subject::class,
      'major_has_subjects'
    )
      ->using(MajorHasSubject::class)
      ->withPivot('semester');
  }

  /**
   * Updates the total course credit for the major by calculating the sum of course credits
   * for all subjects associated with the major, taking into account subjects with the "PILIH SALAH SATU" note.
   *
   * This method groups the subjects by semester, then calculates the total course credit by:
   * 1. Adding the course credits for all subjects without the "PILIH SALAH SATU" note.
   * 2. For subjects with the "PILIH SALAH SATU" note, only adding the maximum course credit from that group.
   * 3. Updating the `total_course_credit` column in the `majors` table with the calculated total.
   */
  public function updateTotalCourseCredit()
  {
    $totalCourseCredit = 0;
    $subjects = $this->subjects;
    $subjectsBySemester = $subjects->groupBy('pivot.semester');

    foreach ($subjectsBySemester as $semester => $subjects) {
      // Pisahkan mata kuliah berdasarkan "PILIH SALAH SATU"
      $withPilihSalahSatu = $subjects->filter(function ($subject) {
        return str_contains($subject->note, SubjectNote::PS->value);
      });

      $withoutPilihSalahSatu = $subjects->filter(function ($subject) {
        return !str_contains($subject->note, SubjectNote::PS->value);
      });

      // Tambahkan total SKS dari mata kuliah tanpa "PILIH SALAH SATU"
      foreach ($withoutPilihSalahSatu as $subject) {
        $totalCourseCredit += $subject->course_credit; // Mengambil SKS dari kolom course_credit di tabel subjects
      }

      // Jika ada mata kuliah "PILIH SALAH SATU", hanya tambahkan salah satu dari grup ini
      if ($withPilihSalahSatu->isNotEmpty()) {
        $totalCourseCredit += $withPilihSalahSatu->max()->course_credit;
      }
    }

    // Update nilai total_course_credit pada tabel majors
    $this->update(['total_course_credit' => $totalCourseCredit]);
  }
}
