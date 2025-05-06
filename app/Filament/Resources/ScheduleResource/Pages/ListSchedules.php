<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Forms;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classroom.name')
                    ->label('Classroom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('day_of_week')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Monday' => 'primary',
                        'Tuesday' => 'success',
                        'Wednesday' => 'warning',
                        'Thursday' => 'danger',
                        'Friday' => 'info',
                        'Saturday' => 'gray',
                        'Sunday' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('start_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('day_of_week', 'asc')
            ->filters([
                SelectFilter::make('day_of_week')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ])
                    ->multiple()
                    ->label('Days'),
                SelectFilter::make('classroom')
                    ->relationship('classroom', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('teacher')
                    ->relationship('teacher', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('time_range')
                    ->form([
                        TimePicker::make('start_time_from')
                            ->label('Start Time From'),
                        TimePicker::make('start_time_until')
                            ->label('Start Time Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_time_from'],
                                fn (Builder $query, $time): Builder => $query->where('start_time', '>=', $time),
                            )
                            ->when(
                                $data['start_time_until'],
                                fn (Builder $query, $time): Builder => $query->where('start_time', '<=', $time),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_time_from'] ?? null) {
                            $indicators['start_time_from'] = 'Start time from ' . $data['start_time_from'];
                        }
                        if ($data['start_time_until'] ?? null) {
                            $indicators['start_time_until'] = 'Start time until ' . $data['start_time_until'];
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->emptyStateHeading('No schedules found')
            ->emptyStateDescription('Create a new schedule to get started.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
