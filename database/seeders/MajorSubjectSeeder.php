<?php

namespace Database\Seeders;

use App\Imports\Academics\MajorSubjectImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class MajorSubjectSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $file = public_path('assets/excels/template-master-data-prodi-matakuliah.xlsx');
    Excel::import(new MajorSubjectImport, $file);
  }
}
