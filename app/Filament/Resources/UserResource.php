<?php

namespace App\Filament\Resources;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class UserResource extends Resource implements HasShieldPermissions
{
  protected static ?string $model = User::class;

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
            Forms\Components\TextInput::make('name')
              ->label(trans('pages-users::page.label.name'))
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('email')
              ->label(trans('pages-users::page.label.email'))
              ->email()
              ->unique(ignoreRecord: true)
              ->required()
              ->maxLength(255),

            PhoneInput::make('phone')
              ->defaultCountry('ID')
              ->nullable()
              ->unique(ignoreRecord: true)
              ->live()
              ->displayNumberFormat(PhoneInputNumberType::E164),

          ])->columns(3),

        Forms\Components\Grid::make(2)
          ->schema([
            Forms\Components\Section::make()
              ->schema([

                Forms\Components\FileUpload::make('profile_photo_path')
                  ->label(trans('pages-users::page.label.profile_photo_path'))
                  ->avatar()
                  ->image()
                  ->disk('public')
                  ->directory('profile-photos')
                  ->visibility('public')
                  ->rules(['nullable', 'mimes:jpg,jpeg,png', 'max:2048'])
                  ->extraAttributes(['class' => 'flex items-center justify-center']),

              ])->columnSpan(1),

            Forms\Components\Section::make()
              ->schema([

                Forms\Components\ToggleButtons::make('status')
                  ->label(trans('pages-users::page.label.status'))
                  ->inline()
                  ->options(AccountStatus::class)
                  ->required(),

                Forms\Components\Select::make('roles')
                  ->label(trans('pages-users::page.label.roles'))
                  ->relationship(name: 'roles', titleAttribute: 'name')
                  ->getOptionLabelFromRecordUsing(
                    fn(Model $record) => UserRole::from($record->name)->getLabel()
                  )
                  ->searchable()
                  ->preload()
                  ->required(),

              ])->columnSpan(1),
          ]),

      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\ImageColumn::make('profile_photo_url')
          ->label(trans('pages-users::page.label.profile_photo_path'))
          ->circular(),
        Tables\Columns\TextColumn::make('name')
          ->label(trans('pages-users::page.label.name'))
          ->searchable(),
        Tables\Columns\TextColumn::make('email')
          ->label(trans('pages-users::page.label.email'))
          ->searchable(),
        PhoneColumn::make('phone')
          ->label(trans('pages-users::page.label.phone'))
          ->displayFormat(PhoneInputNumberType::E164)
          ->getStateUsing(fn(Model $record) => $record->phone ?: '-')
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('roles.name')
          ->badge()
          ->getStateUsing(function (Model $record) {
            return $record->roles->map(function ($role) {
              return UserRole::from($role->getRawOriginal('name'))->getLabel();
            })->implode(', ') ?: '-';
          }),
        Tables\Columns\TextColumn::make('email_verified_at')
          ->label(trans('pages-users::page.label.email_verified_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('status')
          ->label(trans('pages-users::page.label.status'))
          ->badge(),
        Tables\Columns\TextColumn::make('created_at')
          ->label(trans('pages-users::page.label.created_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->label(trans('pages-users::page.label.updated_at'))
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make(trans('filament-shield::filament-shield.resource.label.role'))
          ->label(trans('pages-users::page.label.filter.roles'))
          ->relationship('roles', 'name')
          ->searchable()
          ->preload()
          ->getOptionLabelFromRecordUsing(
            fn(Model $record) => UserRole::from($record->name)->getLabel()
          )
          ->indicator(trans('filament-shield::filament-shield.resource.label.role')),

        Tables\Filters\SelectFilter::make('status')
          ->label(trans('pages-users::page.label.filter.status'))
          ->options(
            Collection::make(AccountStatus::cases())
              ->mapWithKeys(
                fn(AccountStatus $enum) => [$enum->value => $enum->getLabel()]
              )
          )
          ->indicator('Status')
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
            ->visible(
              fn(Model $record) => $record->roles->implode('name') !== UserRole::SuperAdmin->value
            ),
          Tables\Actions\DeleteAction::make()
            ->iconSize('sm')
            ->hidden(function (Model $record) {
              $isSuperAdmin = $record->roles->contains('name', UserRole::SuperAdmin->value);
              $isActive = $record->status->value !== AccountStatus::InActive->value;

              return $isSuperAdmin || ($isActive && !$isSuperAdmin);
            })
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.delete.title'))
                ->body(trans('notification.delete.body', ['label' => trans('pages-users::page.resource.label.user')])),
            ),
        ])
          ->button()
          ->size(ActionSize::Small)
          ->icon('heroicon-m-ellipsis-vertical'),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ])
      ->defaultPaginationPageOption(5)
      ->recordUrl(null)
      ->checkIfRecordIsSelectableUsing(
        fn(Model $record): bool => $record->roles->implode('name') !== UserRole::SuperAdmin->value
      )
      ->groups([
        Tables\Grouping\Group::make('created_at')
          ->label(trans('pages-users::page.label.group.created_at'))
          ->date()
          ->collapsible(),
      ]);
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
      'index' => Pages\ListUsers::route('/'),
      'create' => Pages\CreateUser::route('/create'),
      'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
  }

  public static function getNavigationGroup(): ?string
  {
    return trans('navigation.settings.group');
  }

  public static function getNavigationIcon(): string
  {
    return trans('pages-users::page.nav.user.icon');
  }

  public static function getNavigationLabel(): string
  {
    return trans('pages-users::page.nav.user.label');
  }

  public static function getModelLabel(): string
  {
    return trans('pages-users::page.resource.label.user');
  }

  public static function getPluralModelLabel(): string
  {
    return trans('pages-users::page.resource.label.users');
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
