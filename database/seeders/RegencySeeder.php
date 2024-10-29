<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Regency;

class RegencySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Path ke file json
    $json = File::get(public_path('assets/json/regencies.json'));

    // Decode JSON ke array
    $data = json_decode($json, true);

    $chunks = array_chunk($data, 1000);
    foreach ($chunks as $chunk) {
      foreach ($chunk as &$item) {
        $item['uuid'] = (string) Str::uuid();
        $item['created_at'] = now();
        $item['updated_at'] = now();
      }

      // Save to Database
      Regency::insert($chunk);
    }
  }
}
