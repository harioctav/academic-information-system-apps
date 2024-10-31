<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageResource\Pages;
use App\Filament\Resources\VillageResource\RelationManagers;
use App\Models\District;
use App\Models\Village;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VillageResource extends Resource implements HasShieldPermissions
{
  protected static ?string $model = Village::class;

  protected static ?int $navigationSort = 4;

  public static function getPermissionPrefixes(): array
  {
    return [
      'view',
      'view_any',
      'create',
      'update',
      'delete',
      'delete_any',
    ];
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make()
          ->schema([

            Forms\Components\Select::make('district_id')
              ->relationship(name: 'district')
              ->label(trans('pages-districts::page.resource.label.district'))
              ->searchable()
              ->getOptionLabelFromRecordUsing(function (?Model $record) {
                return "{$record->name} - {$record->regency->formatted_name}, {$record->regency->province->name}";
              })
              ->getSearchResultsUsing(function (string $search): array {
                $keyword = "%$search%";

                return District::query()
                  ->with(['regency.province'])
                  ->where(function (Builder $builder) use ($keyword) {
                    $builder->where('name', 'like', $keyword)
                      ->orWhereHas('regency', function (Builder $subBuilder) use ($keyword) {
                        $subBuilder->where('name', 'like', $keyword)->orWhere('type', 'like', $keyword);
                      });
                  })
                  ->limit(20)
                  ->get()
                  ->mapWithKeys(function (District $district) {
                    return [$district->id => $district->name];
                  })
                  ->toArray();
              })
              ->preload()
              ->columnSpanFull()
              ->required(),

            Forms\Components\Grid::make(3)
              ->schema([
                Forms\Components\TextInput::make('name')
                  ->label(trans('pages-villages::page.label.name'))
                  ->required()
                  ->maxLength(50),
                Forms\Components\TextInput::make('code')
                  ->label(trans('pages-villages::page.label.code'))
                  ->required()
                  ->maxLength(6),
                Forms\Components\TextInput::make('pos_code')
                  ->label(trans('pages-villages::page.label.pos_code'))
                  ->required()
                  ->unique(ignoreRecord: true)
                  ->maxLength(10),
              ])
          ])->columns(1),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('No')
          ->rowIndex(),
        Tables\Columns\TextColumn::make('district.regency.province.name')
          ->label(trans('pages-provinces::page.resource.label.province'))
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('district.regency.formatted_name')
          ->label(trans('pages-regencies::page.resource.label.regency'))
          ->searchable(),
        Tables\Columns\TextColumn::make('district.name')
          ->label(trans('pages-districts::page.resource.label.district'))
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('name')
          ->label(trans('pages-villages::page.label.name'))
          ->searchable(),
        Tables\Columns\TextColumn::make('pos_code')
          ->label(trans('pages-villages::page.label.pos_code'))
          ->searchable(),
        Tables\Columns\TextColumn::make('code')
          ->label(trans('pages-villages::page.label.code'))
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('full_code')
          ->label(trans('pages-villages::page.label.full_code'))
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('created_at')
          ->label(trans('pages-villages::page.label.created_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->label(trans('pages-villages::page.label.updated_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\ViewAction::make()
            ->iconSize('sm')
            ->color('info'),
          Tables\Actions\EditAction::make()
            ->color('warning')
            ->icon('heroicon-m-pencil')
            ->iconSize('sm')
            ->mutateFormDataUsing(function (array $data): array {
              if (isset($data['district_id'])) {
                $district = District::findOrFail($data['district_id']);
                $data['full_code'] = $district->full_code . $data['code'];
              }

              return $data;
            })
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.edit.title'))
                ->body(trans('notification.edit.body', ['label' => trans('pages-villages::page.resource.label.village')])),
            ),
          Tables\Actions\DeleteAction::make()
            ->iconSize('sm')
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.delete.title'))
                ->body(trans('notification.delete.body', ['label' => trans('pages-villages::page.resource.label.village')])),
            ),
        ])
          ->button()
          ->size('sm')
          ->icon('heroicon-m-ellipsis-vertical'),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make()
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.delete.title'))
                ->body(trans('notification.delete.body', ['label' => trans('pages-villages::page.resource.label.village')])),
            ),
        ]),
      ])
      ->defaultPaginationPageOption(5);
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListVillages::route('/'),
    ];
  }

  public static function getNavigationGroup(): ?string
  {
    return trans('navigation.regions.group');
  }

  public static function getNavigationIcon(): string
  {
    return trans('pages-villages::page.nav.village.icon');
  }

  public static function getNavigationLabel(): string
  {
    return trans('pages-villages::page.nav.village.label');
  }

  public static function getModelLabel(): string
  {
    return trans('pages-villages::page.resource.label.village');
  }

  public static function getPluralModelLabel(): string
  {
    return trans('pages-villages::page.resource.label.villages');
  }

  public static function getNavigationBadge(): ?string
  {
    return static::getModel()::count();
  }

  public static function getNavigationBadgeColor(): ?string
  {
    return static::getModel()::count() > 100 ? 'warning' : 'primary';
  }
}
