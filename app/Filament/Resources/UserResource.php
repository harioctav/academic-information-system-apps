<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
        Forms\Components\TextInput::make('uuid')
          ->label('UUID')
          ->required()
          ->maxLength(255),
        Forms\Components\TextInput::make('name')
          ->required()
          ->maxLength(255),
        Forms\Components\TextInput::make('email')
          ->email()
          ->required()
          ->maxLength(255),
        Forms\Components\DateTimePicker::make('email_verified_at'),
        Forms\Components\TextInput::make('password')
          ->password()
          ->required()
          ->maxLength(255),
        Forms\Components\Textarea::make('two_factor_secret')
          ->columnSpanFull(),
        Forms\Components\Textarea::make('two_factor_recovery_codes')
          ->columnSpanFull(),
        Forms\Components\DateTimePicker::make('two_factor_confirmed_at'),
        Forms\Components\TextInput::make('current_team_id')
          ->numeric(),
        Forms\Components\TextInput::make('profile_photo_path')
          ->maxLength(2048),
        Forms\Components\Toggle::make('status')
          ->required(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('uuid')
          ->label('UUID')
          ->searchable(),
        Tables\Columns\TextColumn::make('name')
          ->searchable(),
        Tables\Columns\TextColumn::make('email')
          ->searchable(),
        Tables\Columns\TextColumn::make('email_verified_at')
          ->dateTime()
          ->sortable(),
        Tables\Columns\TextColumn::make('two_factor_confirmed_at')
          ->dateTime()
          ->sortable(),
        Tables\Columns\TextColumn::make('current_team_id')
          ->numeric()
          ->sortable(),
        Tables\Columns\TextColumn::make('profile_photo_path')
          ->searchable(),
        Tables\Columns\IconColumn::make('status')
          ->boolean(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
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
