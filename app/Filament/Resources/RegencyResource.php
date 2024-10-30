<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegencyResource\Pages;
use App\Filament\Resources\RegencyResource\RelationManagers;
use App\Models\Regency;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\RegencyType;
use Illuminate\Support\Collection;

class RegencyResource extends Resource implements HasShieldPermissions
{
  protected static ?string $model = Regency::class;

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
        // Forms\Components\TextInput::make('uuid')
        //   ->label('UUID')
        //   ->required()
        //   ->maxLength(255),
        // Forms\Components\Select::make('province_id')
        //   ->relationship('province', 'name')
        //   ->required(),
        // Forms\Components\TextInput::make('type')
        //   ->required()
        //   ->maxLength(255),
        // Forms\Components\TextInput::make('name')
        //   ->required()
        //   ->maxLength(255),
        // Forms\Components\TextInput::make('code')
        //   ->required()
        //   ->maxLength(255),
        // Forms\Components\TextInput::make('full_code')
        //   ->required()
        //   ->maxLength(255),
        Forms\Components\Section::make()
          ->schema([
            Forms\Components\Select::make('province_id')
              ->relationship(name: 'province', titleAttribute: 'name')
              ->label(trans('pages-provinces::page.resource.label.province'))
              ->searchable()
              ->preload()
              ->required(),
          ]),
        Forms\Components\Section::make()
          ->schema([
            Forms\Components\TextInput::make('code')
              ->label(trans('pages-regencies::page.label.code'))
              ->required()
              ->maxLength(5),
            Forms\Components\TextInput::make('name')
              ->label(trans('pages-regencies::page.label.name'))
              ->required()
              ->maxLength(80),
            Forms\Components\ToggleButtons::make('type')
              ->label(trans('pages-regencies::page.label.type'))
              ->inline()
              ->options(RegencyType::class)
              ->required(),
          ])->columns(3),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('No')
          ->rowIndex(),
        Tables\Columns\TextColumn::make('province.name')
          ->label(trans('pages-provinces::page.resource.label.province'))
          ->numeric()
          ->sortable(),
        Tables\Columns\TextColumn::make('type')
          ->label(trans('pages-regencies::page.label.type'))
          ->badge(),
        Tables\Columns\TextColumn::make('name')
          ->label(trans('pages-regencies::page.label.name'))
          ->searchable(),
        Tables\Columns\TextColumn::make('code')
          ->label(trans('pages-regencies::page.label.code'))
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('full_code')
          ->label(trans('pages-regencies::page.label.full_code'))
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('districts_count')
          ->label(trans('pages-regencies::page.label.districts_count'))
          ->counts('districts')
          ->badge()
          ->sortable()
          ->colors(['info']),
        Tables\Columns\TextColumn::make('created_at')
          ->label(trans('pages-regencies::page.label.created_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->label(trans('pages-regencies::page.label.updated_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('province')
          ->label(trans('pages-regencies::page.label.filter.provinces'))
          ->relationship('province', 'name')
          ->searchable()
          ->preload()
          ->indicator(trans('pages-provinces::page.resource.label.province')),
        Tables\Filters\SelectFilter::make('type')
          ->label(trans('pages-regencies::page.label.filter.types'))
          ->options(
            Collection::make(RegencyType::cases())
              ->mapWithKeys(
                fn(RegencyType $enum) => [$enum->value => $enum->getLabel()]
              )
          )
          ->indicator(trans('pages-regencies::page.label.type'))
          ->native(false),
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
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.edit.title'))
                ->body(trans('notification.edit.body', ['label' => trans('pages-regencies::page.resource.label.regency')])),
            ),
          Tables\Actions\DeleteAction::make()
            ->iconSize('sm')
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.delete.title'))
                ->body(trans('notification.delete.body', ['label' => trans('pages-regencies::page.resource.label.regency')])),
            ),
        ])
          ->button()
          ->size('sm')
          ->icon('heroicon-m-ellipsis-vertical'),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
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
      'index' => Pages\ListRegencies::route('/'),
    ];
  }

  public static function getNavigationGroup(): ?string
  {
    return trans('navigation.regions.group');
  }

  public static function getNavigationIcon(): string
  {
    return trans('pages-regencies::page.nav.regency.icon');
  }

  public static function getNavigationLabel(): string
  {
    return trans('pages-regencies::page.nav.regency.label');
  }

  public static function getModelLabel(): string
  {
    return trans('pages-regencies::page.resource.label.regency');
  }

  public static function getPluralModelLabel(): string
  {
    return trans('pages-regencies::page.resource.label.regencies');
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
