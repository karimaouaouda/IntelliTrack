<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set check_out to null if not provided
        if (empty($data['check_out'])) {
            $data['check_out'] = null;
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Attendance record created successfully';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('attendable_type')
                    ->label('Type')
                    ->options([
                        'App\Models\User' => 'Teacher',
                        'App\Models\Student' => 'Student',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('attendable_id', null)),

                Forms\Components\Select::make('attendable_id')
                    ->label('Person')
                    ->searchable()
                    ->preload()
                    ->options(function (Forms\Get $get) {
                        $type = $get('attendable_type');
                        if (!$type) return [];

                        return match ($type) {
                            'App\Models\User' => \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))
                                ->pluck('name', 'id'),
                            'App\Models\Student' => \App\Models\Student::pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->required(),

                Forms\Components\Select::make('classroom_id')
                    ->relationship('classroom', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('device_id')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('check_in')
                    ->required()
                    ->default(now()),

                Forms\Components\DateTimePicker::make('check_out')
                    ->nullable()
                    ->after('check_in'),

                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
} 