<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('classroom_id', null))
                    ->label('Teacher'),

                Select::make('classroom_id')
                    ->relationship('classroom', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->label('Classroom'),

                Select::make('day_of_week')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ])
                    ->required()
                    ->live()
                    ->label('Day of Week'),

                TimePicker::make('start_time')
                    ->required()
                    ->live()
                    ->label('Start Time'),

                TimePicker::make('end_time')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $startTime = $get('start_time');
                        if ($startTime && $state && $startTime >= $state) {
                            $set('end_time', null);
                            $this->addError('end_time', 'End time must be after start time.');
                        }
                    })
                    ->label('End Time'),

                TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->label('Subject'),

                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('Description'),
            ])
            ->columns(2);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Check for schedule conflicts, excluding the current schedule
        $conflicts = DB::table('schedules')
            ->where('teacher_id', $data['teacher_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('id', '!=', $this->record->id)
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($conflicts) {
            $this->addError('start_time', 'This time slot conflicts with an existing schedule for the selected teacher.');
            $this->addError('end_time', 'This time slot conflicts with an existing schedule for the selected teacher.');
            return [];
        }

        // Check for classroom availability, excluding the current schedule
        $classroomConflicts = DB::table('schedules')
            ->where('classroom_id', $data['classroom_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('id', '!=', $this->record->id)
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($classroomConflicts) {
            $this->addError('start_time', 'This time slot conflicts with an existing schedule for the selected classroom.');
            $this->addError('end_time', 'This time slot conflicts with an existing schedule for the selected classroom.');
            return [];
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('duplicate')
                ->label('Duplicate Schedule')
                ->icon('heroicon-o-document-duplicate')
                ->action(function () {
                    $newSchedule = $this->record->replicate();
                    $newSchedule->save();
                    
                    $this->redirect(ScheduleResource::getUrl('edit', ['record' => $newSchedule]));
                }),
            Action::make('delete')
                ->label('Delete Schedule')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->record->delete())
                ->after(fn () => $this->redirect(ScheduleResource::getUrl('index'))),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 