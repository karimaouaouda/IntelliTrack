<?php

namespace App\Filament\Resources\UserProfileResource\Pages;

use App\Filament\Resources\UserProfileResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Actions;

class ViewUserProfile extends ViewRecord
{
    protected static string $resource = UserProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_schedule')
                ->label('View Schedule')
                ->icon('heroicon-o-calendar')
                ->url(fn () => route('filament.admin.resources.schedules.index', ['tableFilters[teacher_id]' => $this->record->id]))
                ->visible(fn () => $this->record->isTeacher())
                ->openUrlInNewTab(),

            Action::make('view_classrooms')
                ->label('View Classrooms')
                ->icon('heroicon-o-academic-cap')
                ->url(fn () => route('filament.admin.resources.classrooms.index', ['tableFilters[teachers]' => $this->record->id]))
                ->visible(fn () => $this->record->isTeacher())
                ->openUrlInNewTab(),

            EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('Basic Information')
                            ->schema([
                                ImageEntry::make('teacherProfile.profile_photo')
                                    ->label('Profile Photo')
                                    ->circular()
                                    ->defaultImageUrl(fn ($record) => "https://ui-avatars.com/api/?name={$record->name}&background=random")
                                    ->columnSpanFull(),

                                TextEntry::make('name')
                                    ->label('Name'),

                                TextEntry::make('email')
                                    ->label('Email'),

                                TextEntry::make('ref_id')
                                    ->label('Reference ID'),

                                TextEntry::make('roles.name')
                                    ->label('Roles')
                                    ->badge(),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),
                            ]),

                        Section::make('Teacher Profile')
                            ->schema([
                                Tabs::make('Teacher Information')
                                    ->tabs([
                                        Tabs\Tab::make('Professional Information')
                                            ->schema([
                                                TextEntry::make('teacherProfile.subject')
                                                    ->label('Subject'),

                                                TextEntry::make('teacherProfile.qualification')
                                                    ->label('Qualification'),

                                                TextEntry::make('teacherProfile.experience_years')
                                                    ->label('Experience')
                                                    ->suffix(' years'),

                                                TextEntry::make('teacherProfile.specialization')
                                                    ->label('Specialization'),

                                                TextEntry::make('teacherProfile.bio')
                                                    ->label('Bio')
                                                    ->markdown(),

                                                RepeatableEntry::make('teacherProfile.certifications')
                                                    ->label('Certifications')
                                                    ->schema([
                                                        TextEntry::make('certification')
                                                            ->label('Certification'),
                                                    ]),

                                                RepeatableEntry::make('teacherProfile.skills')
                                                    ->label('Skills')
                                                    ->schema([
                                                        TextEntry::make('skill')
                                                            ->label('Skill'),
                                                    ]),
                                            ]),

                                        Tabs\Tab::make('Contact Information')
                                            ->schema([
                                                TextEntry::make('teacherProfile.phone')
                                                    ->label('Phone'),

                                                TextEntry::make('teacherProfile.address')
                                                    ->label('Address'),

                                                TextEntry::make('teacherProfile.emergency_contact')
                                                    ->label('Emergency Contact'),
                                            ]),

                                        Tabs\Tab::make('Employment Details')
                                            ->schema([
                                                TextEntry::make('teacherProfile.joining_date')
                                                    ->label('Joining Date')
                                                    ->date(),

                                                TextEntry::make('teacherProfile.contract_end_date')
                                                    ->label('Contract End Date')
                                                    ->date(),

                                                TextEntry::make('teacherProfile.employment_status')
                                                    ->label('Employment Status')
                                                    ->badge()
                                                    ->color(fn (string $state): string => match ($state) {
                                                        'active' => 'success',
                                                        'on_leave' => 'warning',
                                                        'terminated' => 'danger',
                                                    }),

                                                TextEntry::make('teacherProfile.salary')
                                                    ->label('Salary')
                                                    ->money('USD'),

                                                RepeatableEntry::make('teacherProfile.working_hours')
                                                    ->label('Working Hours')
                                                    ->schema([
                                                        TextEntry::make('day')
                                                            ->label('Day'),
                                                        TextEntry::make('hours')
                                                            ->label('Hours'),
                                                    ]),

                                                TextEntry::make('teacherProfile.notes')
                                                    ->label('Notes')
                                                    ->markdown(),
                                            ]),

                                        Tabs\Tab::make('Schedule')
                                            ->schema([
                                                RepeatableEntry::make('schedules')
                                                    ->label('Schedule')
                                                    ->schema([
                                                        TextEntry::make('day')
                                                            ->label('Day'),
                                                        TextEntry::make('start_time')
                                                            ->label('Start Time'),
                                                        TextEntry::make('end_time')
                                                            ->label('End Time'),
                                                        TextEntry::make('classroom.name')
                                                            ->label('Classroom'),
                                                        TextEntry::make('subject')
                                                            ->label('Subject'),
                                                    ])
                                                    ->columns(5),
                                            ]),

                                        Tabs\Tab::make('Classrooms')
                                            ->schema([
                                                RepeatableEntry::make('classrooms')
                                                    ->label('Classrooms')
                                                    ->schema([
                                                        TextEntry::make('name')
                                                            ->label('Name'),
                                                        TextEntry::make('capacity')
                                                            ->label('Capacity'),
                                                        TextEntry::make('location')
                                                            ->label('Location'),
                                                        TextEntry::make('status')
                                                            ->label('Status')
                                                            ->badge()
                                                            ->color(fn (string $state): string => match ($state) {
                                                                'active' => 'success',
                                                                'maintenance' => 'warning',
                                                                'inactive' => 'danger',
                                                            }),
                                                    ])
                                                    ->columns(4),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn () => $this->record->isTeacher()),
                    ]),
            ]);
    }
} 