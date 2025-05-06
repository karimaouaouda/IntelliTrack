<?php

namespace App\Filament\Resources\ClassroomResource\Pages;

use App\Filament\Resources\ClassroomResource;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ViewClassroomSchedule extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ClassroomResource::class;

    protected static string $view = 'filament.resources.classroom-resource.pages.view-classroom-schedule';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->getResource()::getModel()::query()
                    ->where('id', $this->record->id)
                    ->with('schedules.teacher')
            )
            ->columns([
                TextColumn::make('schedules.day_of_week')
                    ->label('Day')
                    ->sortable(),
                TextColumn::make('schedules.start_time')
                    ->label('Start Time')
                    ->time(),
                TextColumn::make('schedules.end_time')
                    ->label('End Time')
                    ->time(),
                TextColumn::make('schedules.subject')
                    ->label('Subject'),
                TextColumn::make('schedules.teacher.name')
                    ->label('Teacher'),
            ])
            ->defaultSort('schedules.day_of_week')
            ->paginated(false);
    }
} 