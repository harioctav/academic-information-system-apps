<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Filament\Resources\DistrictResource\RelationManagers;
use App\Models\District;
use App\Models\Regency;
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

class DistrictResource extends Resource implements HasShieldPermissions
{
  protected static ?string $model = District::class;
  protected static ?int $navigationSort = 3;
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
            Forms\Components\Select::make('regency_id')
              ->relationship(
                name: 'regency',
                titleAttribute: 'name'
              )
              ->searchable()
              ->getOptionLabelFromRecordUsing(
                fn(?Model $record) => $record->formatted_name
              )
              ->preload()
              ->required(),
            Forms\Components\TextInput::make('code')
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('name')
              ->required()
              ->maxLength(255),
          ])->columns(3),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('No')
          ->rowIndex(),
        Tables\Columns\TextColumn::make('regency.province.name')
          ->label(trans('pages-provinces::page.resource.label.province'))
          ->sortable(),
        Tables\Columns\TextColumn::make('regency.name')
          ->label(trans('pages-regencies::page.resource.label.regency'))
          ->sortable()
          ->getStateUsing(
            fn(?Model $record) => $record->regency->formatted_name
          ),
        Tables\Columns\TextColumn::make('name')
          ->label(trans('pages-districts::page.label.name'))
          ->searchable(),
        Tables\Columns\TextColumn::make('villages_count')
          ->label(trans('pages-districts::page.label.villages_count'))
          ->counts('villages')
          ->badge()
          ->colors(['info']),
        Tables\Columns\TextColumn::make('code')
          ->label(trans('pages-districts::page.label.code'))
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('full_code')
          ->label(trans('pages-districts::page.label.full_code'))
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('created_at')
          ->label(trans('pages-districts::page.label.created_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->label(trans('pages-districts::page.label.updated_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('Regency')
          ->relationship(
            name: 'regency',
            titleAttribute: 'name'
          )
          ->label(trans('pages-districts::page.label.filter.regencies'))
          ->searchable()
          ->getOptionLabelFromRecordUsing(
            fn(?Model $record) => $record->formatted_name
          )
          ->preload()
          ->indicator(trans('pages-regencies::page.resource.label.regency')),
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
              if (isset($data['regency_id'])) {
                $regency = Regency::findOrFail($data['regency_id']);
                $data['full_code'] = $regency->full_code . $data['code'];
              }

              return $data;
            })
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.edit.title'))
                ->body(trans('notification.edit.body', ['label' => trans('pages-districts::page.resource.label.district')])),
            ),
          Tables\Actions\DeleteAction::make()
            ->iconSize('sm')
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.delete.title'))
                ->body(trans('notification.delete.body', ['label' => trans('pages-districts::page.resource.label.district')])),
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
                ->body(trans('notification.delete.body', ['label' => trans('pages-districts::page.resource.label.district')])),
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
      'index' => Pages\ListDistricts::route('/'),
    ];
  }

  public static function getNavigationGroup(): ?string
  {
    return trans('navigation.regions.group');
  }

  public static function getNavigationIcon(): string
  {
    return trans('pages-districts::page.nav.district.icon');
  }

  public static function getNavigationLabel(): string
  {
    return trans('pages-districts::page.nav.district.label');
  }

  public static function getModelLabel(): string
  {
    return trans('pages-districts::page.resource.label.district');
  }

  public static function getPluralModelLabel(): string
  {
    return trans('pages-districts::page.resource.label.districts');
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
