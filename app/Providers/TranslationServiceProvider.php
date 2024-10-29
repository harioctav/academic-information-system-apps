<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    $this->loadPagesTranslations();
  }

  private function loadPagesTranslations()
  {
    $pagesPath = lang_path('pages');
    $modules = glob($pagesPath . '/*', GLOB_ONLYDIR);

    foreach ($modules as $module) {
      $name = basename($module);
      $this->loadTranslationsFrom($module, "pages-{$name}");
    }
  }
}
