<?php

namespace App\Filament\Resources\TeacherProfileResource\Pages;

use App\Filament\Resources\TeacherProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;

class ViewTeacherProfile extends ViewRecord
{
    protected static string $resource = TeacherProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Teacher Name'),
                                TextEntry::make('subject'),
                                TextEntry::make('qualification'),
                                TextEntry::make('experience_years')
                                    ->suffix(' years'),
                                TextEntry::make('specialization'),
                                TextEntry::make('employment_status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'on_leave' => 'warning',
                                        'terminated' => 'danger',
                                    }),
                            ]),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('phone'),
                                TextEntry::make('address'),
                                TextEntry::make('emergency_contact')
                                    ->label('Emergency Contact'),
                            ]),
                    ]),

                Section::make('Professional Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('joining_date')
                                    ->date(),
                                TextEntry::make('contract_end_date')
                                    ->date(),
                                TextEntry::make('salary')
                                    ->money('USD'),
                            ]),
                        TextEntry::make('bio')
                            ->markdown()
                            ->columnSpanFull(),
                        TextEntry::make('certifications')
                            ->listWithLineBreaks()
                            ->columnSpanFull(),
                        TextEntry::make('skills')
                            ->listWithLineBreaks()
                            ->columnSpanFull(),
                    ]),

                Section::make('Working Hours')
                    ->schema([
                        TextEntry::make('working_hours')
                            ->listWithLineBreaks()
                            ->columnSpanFull(),
                    ]),

                Section::make('Profile Photo')
                    ->schema([
                        ImageEntry::make('profile_photo')
                            ->columnSpanFull(),
                    ]),

                Section::make('Additional Notes')
                    ->schema([
                        TextEntry::make('notes')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
} 