<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $json = File::get(
      public_path('assets/json/users.json')
    );

    $decode = json_decode($json, true);
    $chunks = array_chunk($decode, 1000);

    // Insert To Database
    foreach ($chunks as $chunk) {
      foreach ($chunk as &$item) {
        $item['uuid'] = (string) Str::uuid();
        $item['password'] = Hash::make('password');
        $item['email_verified_at'] = now();
        $item['created_at'] = now();
        $item['updated_at'] = now();
      }

      // Save to database
      User::insert($chunk);
    }
  }
}
