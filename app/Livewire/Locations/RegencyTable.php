<?php

namespace App\Livewire\Locations;

use App\Models\Province;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Regency;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class RegencyTable extends DataTableComponent
{
  protected $model = Regency::class;

  public function configure(): void
  {
    $this->setPrimaryKey('id');
  }

  public function columns(): array
  {
    return [
      Column::make("Province", "province.name")
        ->sortable(),
      Column::make("Name")
        ->sortable()
        ->searchable()
        ->format(
          fn($value, $row, Column $column) => $row->formatted_name
        ),
      Column::make("Type", "type")
        ->sortable(),
      Column::make("Code", "code")
        ->sortable(),
      Column::make("Full code", "full_code")
        ->sortable(),
      Column::make("Created at", "created_at")
        ->sortable(),
      Column::make("Updated at", "updated_at")
        ->sortable(),
    ];
  }

  public function filters(): array
  {
    return [
      SelectFilter::make('Filter by Province', 'province_id')
        ->setFilterPillTitle('Province')
        ->options(Province::pluck('name', 'id')->toArray())
        ->filter(function (Builder $builder, string $value) {
          $builder->where('province_id', $value);
        }),
    ];
  }
}
