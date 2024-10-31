<?php

namespace App\Filament\Resources\MajorResource\Pages;

use App\Enums\SubjectSemester;
use App\Filament\Resources\MajorResource;
use App\Models\Subject;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Htmlable;

class ManageMajorSubjects extends ManageRelatedRecords
{
  protected static string $resource = MajorResource::class;

  protected static string $relationship = 'subjects';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('subject_id')
          ->label(trans('pages-subjects::page.resource.label.subject'))
          ->options(function () {
            $record = $this->getMountedTableActionRecord();

            if ($record) {
              // Jika ada record, kembalikan subject dengan ID yang sama dengan record
              return Subject::where('id', $record->subject_id)
                ->get()
                ->mapWithKeys(function ($subject) {
                  return [$subject->id => $subject->code . ' - ' . $subject->name];
                });
            }

            // Jika tidak ada record, kembalikan subject yang tidak terkait dengan major
            return Subject::whereNotIn('id', function ($query) {
              $query->select('subject_id')
                ->from('major_has_subjects')
                ->where('major_id', $this->getOwnerRecord()->id);
            })
              ->get()
              ->mapWithKeys(function ($subject) {
                return [$subject->id => $subject->code . ' - ' . $subject->name];
              });
          })
          ->searchable()
          ->live()
          ->preload()
          ->required()
          ->default(function () {
            $record = $this->getMountedTableActionRecord();
            return $record ? $record->subject_id : null;
          })
          ->disabled(fn() => $this->getMountedTableActionRecord() !== null),

        Forms\Components\Select::make('semester')
          ->options(
            Collection::make(SubjectSemester::cases())->mapWithKeys(fn(SubjectSemester $enum) => [$enum->value => $enum->getLabel()])
          )
          ->enum(SubjectSemester::class)
          ->searchable()
          ->required()
          ->native(false),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('name')
      ->columns([
        Tables\Columns\TextColumn::make('code')
          ->label(trans('pages-subjects::page.label.code'))
          ->searchable(),
        Tables\Columns\TextColumn::make('name')
          ->label(trans('pages-subjects::page.label.name'))
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('note')
          ->label(trans('pages-subjects::page.label.note'))
          ->getStateUsing(function (?Model $record) {
            return $record->note ?: '-';
          }),
        Tables\Columns\TextColumn::make('semester'),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('semester')
          ->label(trans('pages-majors::page.label.filter.semester'))
          ->indicator(trans('Semester'))
          ->options(
            Collection::make(SubjectSemester::cases())
              ->mapWithKeys(fn(SubjectSemester $enum) => [$enum->value => $enum->getLabel()])
          )
          ->native(false),
      ])
      ->headerActions([
        Tables\Actions\CreateAction::make()
          ->label(trans('button.create', ['label' => trans('pages-subjects::page.nav.subject.label')]))
          ->size(ActionSize::Small)
          ->icon(trans('button.create.icon'))
          ->iconSize('sm')
          ->before(function (array $data) {
            $existingSubjects = DB::table('major_has_subjects')
              ->where('major_id', $this->getOwnerRecord()->id)
              ->whereIn('subject_id', (array) $data['subject_id'])
              ->exists();

            if ($existingSubjects) {
              Notification::make()
                ->danger()
                ->title(trans('pages-majors::page.validation.uniqe'))
                ->send();

              $this->cancel();
            }
          })
          ->using(function (array $data, string $model): Model {
            if (is_array($data['subject_id'])) {
              $attachData = collect($data['subject_id'])->mapWithKeys(function ($subjectId) use ($data) {
                return [$subjectId => ['semester' => $data['semester']]];
              })->toArray();

              $this->getOwnerRecord()->subjects()->attach($attachData);
              return Subject::find($data['subject_id'][0]);
            }

            $this->getOwnerRecord()->subjects()->attach($data['subject_id'], [
              'semester' => $data['semester']
            ]);

            $subject = Subject::find($data['subject_id']);

            return $subject;
          })
          ->successNotification(
            Notification::make()
              ->success()
              ->title(trans('pages-majors::page.validation.success'))
              ->body(
                trans('pages-major-subjects::page.notification.create', [
                  'major' => $this->getOwnerRecord()->name
                ])
              ),
          ),
      ])
      ->actions([

        Tables\Actions\ActionGroup::make([
          Tables\Actions\EditAction::make()
            ->color('warning')
            ->icon('heroicon-m-pencil')
            ->iconSize('sm')
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.edit.title'))
                ->body(
                  trans('pages-major-subjects::page.notification.edit', [
                    'major' => $this->getOwnerRecord()->name
                  ])
                ),
            ),
          Tables\Actions\DetachAction::make()
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.delete.title'))
                ->body(
                  trans('pages-major-subjects::page.notification.delete', [
                    'major' => $this->getOwnerRecord()->name
                  ])
                ),
            ),
        ])
          ->button()
          ->size('sm')
          ->icon('heroicon-m-ellipsis-vertical'),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DetachBulkAction::make()
            ->successNotification(
              Notification::make()
                ->success()
                ->title(trans('notification.delete.title'))
                ->body(
                  trans('pages-major-subjects::page.notification.delete', [
                    'major' => $this->getOwnerRecord()->name
                  ])
                ),
            ),
        ]),
      ])
      ->recordTitleAttribute('name')
      ->defaultSort('semester')
      ->defaultPaginationPageOption(5);
  }

  public function getTitle(): string|Htmlable
  {
    return trans('pages-major-subjects::page.heading.label', [
      'name' => $this->record->name
    ]);
  }

  public static function getNavigationIcon(): string
  {
    return trans('pages-subjects::page.nav.subject.icon');
  }

  public static function getNavigationLabel(): string
  {
    return trans('pages-subjects::page.nav.subject.label');
  }
}
