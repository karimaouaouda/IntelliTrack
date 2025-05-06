<?php

namespace App\Filament\Resources\TeacherProfileResource\Pages;

use App\Filament\Resources\TeacherProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Illuminate\Database\Eloquent\Model;

class EditTeacherProfile extends EditRecord
{
    protected static string $resource = TeacherProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->before(function (Model $record) {
                    // Add any pre-deletion logic here
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Add any data transformation logic here
        return $data;
    }

    protected function afterSave(): void
    {
        // Add any post-save logic here
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->unique(ignoreRecord: true)
                    ->label('Teacher')
                    ->disabled()
                    ->helperText('The teacher associated with this profile'),

                TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->helperText('The main subject taught by this teacher'),

                TextInput::make('qualification')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Highest academic qualification'),

                TextInput::make('experience_years')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(50)
                    ->helperText('Years of teaching experience'),

                TextInput::make('specialization')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Area of specialization within the subject'),

                Textarea::make('bio')
                    ->maxLength(1000)
                    ->columnSpanFull()
                    ->helperText('A brief biography of the teacher'),

                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255)
                    ->helperText('Contact phone number'),

                TextInput::make('address')
                    ->maxLength(255)
                    ->helperText('Current residential address'),

                TextInput::make('emergency_contact')
                    ->maxLength(255)
                    ->helperText('Emergency contact information'),

                TagsInput::make('certifications')
                    ->columnSpanFull()
                    ->helperText('List of professional certifications'),

                TagsInput::make('skills')
                    ->columnSpanFull()
                    ->helperText('Additional skills and competencies'),

                FileUpload::make('profile_photo')
                    ->image()
                    ->directory('teacher-profiles')
                    ->columnSpanFull()
                    ->helperText('Upload a professional photo'),

                DatePicker::make('joining_date')
                    ->required()
                    ->helperText('Date when the teacher joined the institution'),

                DatePicker::make('contract_end_date')
                    ->helperText('End date of the current contract (if applicable)'),

                Select::make('employment_status')
                    ->options([
                        'active' => 'Active',
                        'on_leave' => 'On Leave',
                        'terminated' => 'Terminated',
                    ])
                    ->required()
                    ->helperText('Current employment status'),

                TextInput::make('salary')
                    ->numeric()
                    ->prefix('$')
                    ->maxValue(1000000)
                    ->helperText('Current salary (if applicable)'),

                KeyValue::make('working_hours')
                    ->keyLabel('Day')
                    ->valueLabel('Hours')
                    ->columnSpanFull()
                    ->helperText('Regular working hours for each day of the week'),

                Textarea::make('notes')
                    ->maxLength(1000)
                    ->columnSpanFull()
                    ->helperText('Additional notes or information'),
            ])
            ->columns(2);
    }
} 