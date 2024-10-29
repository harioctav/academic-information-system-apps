<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasUploadImage
{
  public function getImageUrlAttribute(): string
  {
    return $this->image_path ? Storage::disk($this->getImageDisk()) : $this->defaultImageUrl();
  }

  public function updateImage(null|string $image): void
  {
    tap(
      $this->image_path,
      function ($previous) use ($image) {
        $this->forceFill([
          'image_path' => $image
        ])->save();

        if ($previous && !$image) {
          Storage::disk($this->getImageDisk())->delete($previous);
        }
      }
    );
  }

  public function deleteImage()
  {
    if (is_null($this->image_path)) {
      return;
    }

    Storage::disk($this->getImageDisk())->delete($this->image_path);

    $this->forceFill([
      'image_path' => null,
    ])->save();
  }

  protected function defaultImageUrl(): string
  {
    $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
      return mb_substr($segment, 0, 1);
    })->join(' '));

    return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
  }

  public function getImageDisk(): string
  {
    return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('image-upload.image_disk', 'public');
  }

  public function getImageDirectory(string $directory = 'images'): string
  {
    return config('image-upload.image_directory', $directory);
  }
}
