<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Hash;

class EditTeacher extends EditRecord
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
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->label('New Password')
                    ->helperText('Leave empty to keep current password'),

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
                    ->required()
                    ->label('Role'),
            ])
            ->columns(2);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_schedule')
                ->label('View Schedule')
                ->icon('heroicon-o-calendar')
                ->url(fn () => route('filament.admin.resources.schedules.index', ['tableFilters[teacher][value]' => $this->record->id]))
                ->openUrlInNewTab(),
            Action::make('delete')
                ->label('Delete Teacher')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->record->delete())
                ->after(fn () => $this->redirect(TeacherResource::getUrl('index'))),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 