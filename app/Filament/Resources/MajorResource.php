<?php

namespace App\Filament\Resources;

use App\Enums\DegreeType;
use App\Filament\Resources\MajorResource\Pages;
use App\Filament\Resources\MajorResource\RelationManagers;
use App\Models\Major;
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
use Illuminate\Support\Collection;

class MajorResource extends Resource implements HasShieldPermissions
{
  protected static ?string $model = Major::class;

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
            Forms\Components\TextInput::make('code')
              ->label(trans('pages-majors::page.label.code'))
              ->required()
              ->unique(ignoreRecord: true)
              ->maxLength(5),
            Forms\Components\TextInput::make('name')
              ->label(trans('pages-majors::page.label.name'))
              ->required()
              ->unique(ignoreRecord: true)
              ->maxLength(80),
          ])->columns(),
        Forms\Components\Section::make()
          ->schema([
            Forms\Components\ToggleButtons::make('degree')
              ->label(trans('pages-majors::page.label.degree'))
              ->inline()
              ->options(DegreeType::class)
              ->required(),
          ]),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('No')
          ->rowIndex(),
        Tables\Columns\TextColumn::make('name')
          ->label(trans('pages-majors::page.label.name'))
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('degree')
          ->label(trans('pages-majors::page.label.degree')),
        Tables\Columns\TextColumn::make('total_course_credit')
          ->label(trans('pages-majors::page.label.total_course_credit'))
          ->getStateUsing(
            fn(Model $record) => $record->total_course_credit ?: '-'
          )
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label(trans('pages-majors::page.label.created_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->label(trans('pages-majors::page.label.updated_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('degree')
          ->label(trans('pages-majors::page.label.filter.degrees'))
          ->indicator(trans('pages-majors::page.label.degree'))
          ->options(
            Collection::make(
              DegreeType::cases()
            )->mapWithKeys(
              fn(DegreeType $enum) => [
                $enum->value => $enum->getLabel()
              ]
            )
          )
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
                ->body(trans('notification.edit.body', ['label' => trans('pages-majors::page.resource.label.major')])),
            ),
          Tables\Actions\DeleteAction::make()
            ->iconSize('sm')
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.delete.title'))
                ->body(trans('notification.delete.body', ['label' => trans('pages-majors::page.resource.label.major')])),
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
                ->body(trans('notification.delete.body', ['label' => trans('pages-majors::page.resource.label.major')])),
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
      'index' => Pages\ListMajors::route('/'),
    ];
  }

  public static function getNavigationGroup(): ?string
  {
    return trans('navigation.academics.group');
  }

  public static function getNavigationIcon(): string
  {
    return trans('pages-majors::page.nav.major.icon');
  }

  public static function getNavigationLabel(): string
  {
    return trans('pages-majors::page.nav.major.label');
  }

  public static function getModelLabel(): string
  {
    return trans('pages-majors::page.resource.label.major');
  }

  public static function getPluralModelLabel(): string
  {
    return trans('pages-majors::page.resource.label.majors');
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
