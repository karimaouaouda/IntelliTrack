<?php

namespace App\Filament\Resources\UserProfileResource\Pages;

use App\Filament\Resources\UserProfileResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class CreateUserProfile extends CreateRecord
{
    protected static string $resource = UserProfileResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                if ($operation === 'create') {
                                    $set('ref_id', 'USR-' . strtoupper(Str::random(8)));
                                }
                            }),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->maxLength(255),

                        TextInput::make('ref_id')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),

                        Select::make('roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->required()
                            ->live(),
                    ]),

                Section::make('Teacher Profile')
                    ->schema([
                        Toggle::make('is_teacher')
                            ->label('Is Teacher')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (!$state) {
                                    $set('teacherProfile', null);
                                }
                            }),

                        Section::make('Professional Information')
                            ->schema([
                                TextInput::make('teacherProfile.subject')
                                    ->label('Subject')
                                    ->maxLength(255)
                                    ->required(fn (Get $get) => $get('is_teacher')),

                                TextInput::make('teacherProfile.qualification')
                                    ->label('Qualification')
                                    ->maxLength(255)
                                    ->required(fn (Get $get) => $get('is_teacher')),

                                TextInput::make('teacherProfile.experience_years')
                                    ->label('Experience Years')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(50)
                                    ->required(fn (Get $get) => $get('is_teacher')),

                                TextInput::make('teacherProfile.specialization')
                                    ->label('Specialization')
                                    ->maxLength(255),

                                Textarea::make('teacherProfile.bio')
                                    ->label('Bio')
                                    ->maxLength(1000),

                                TagsInput::make('teacherProfile.certifications')
                                    ->label('Certifications'),

                                TagsInput::make('teacherProfile.skills')
                                    ->label('Skills'),
                            ])
                            ->columns(2)
                            ->visible(fn (Get $get) => $get('is_teacher')),

                        Section::make('Contact Information')
                            ->schema([
                                TextInput::make('teacherProfile.phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->required(fn (Get $get) => $get('is_teacher')),

                                TextInput::make('teacherProfile.address')
                                    ->label('Address')
                                    ->maxLength(255),

                                TextInput::make('teacherProfile.emergency_contact')
                                    ->label('Emergency Contact')
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->visible(fn (Get $get) => $get('is_teacher')),

                        Section::make('Employment Details')
                            ->schema([
                                FileUpload::make('teacherProfile.profile_photo')
                                    ->label('Profile Photo')
                                    ->image()
                                    ->directory('teacher-profiles')
                                    ->maxSize(5120),

                                DatePicker::make('teacherProfile.joining_date')
                                    ->label('Joining Date')
                                    ->required(fn (Get $get) => $get('is_teacher')),

                                DatePicker::make('teacherProfile.contract_end_date')
                                    ->label('Contract End Date'),

                                Select::make('teacherProfile.employment_status')
                                    ->label('Employment Status')
                                    ->options([
                                        'active' => 'Active',
                                        'on_leave' => 'On Leave',
                                        'terminated' => 'Terminated',
                                    ])
                                    ->default('active')
                                    ->required(fn (Get $get) => $get('is_teacher')),

                                TextInput::make('teacherProfile.salary')
                                    ->label('Salary')
                                    ->numeric()
                                    ->prefix('$')
                                    ->maxValue(1000000),

                                KeyValue::make('teacherProfile.working_hours')
                                    ->label('Working Hours')
                                    ->keyLabel('Day')
                                    ->valueLabel('Hours'),

                                Textarea::make('teacherProfile.notes')
                                    ->label('Notes')
                                    ->maxLength(1000),
                            ])
                            ->columns(2)
                            ->visible(fn (Get $get) => $get('is_teacher')),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 