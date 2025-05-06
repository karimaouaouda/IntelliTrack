<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Full Name'),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Email Address'),

                TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->label('Password'),

                TextInput::make('ref_id')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Reference ID')
                    ->helperText('A unique identifier for the teacher (e.g., TCH-001)'),

                Select::make('role')
                    ->options([
                        'teacher' => 'Teacher',
                        'head_teacher' => 'Head Teacher',
                        'subject_teacher' => 'Subject Teacher',
                    ])
                    ->default('teacher')
                    ->required()
                    ->label('Role'),
            ])
            ->columns(2);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'teacher'; // Ensure role is set to teacher
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 