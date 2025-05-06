<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

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
                TextColumn::make('attendable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\Models\User' => 'Teacher',
                        'App\Models\Student' => 'Student',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attendable.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),

//                TextColumn::make('classroom.name')
//                    ->searchable()
//                    ->sortable(),

                TextColumn::make('device_id')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('recorded_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('attendable_type')
                    ->label('Type')
                    ->options([
                        'App\Models\User' => 'Teacher',
                        'App\Models\Student' => 'Student',
                    ]),

//                Filter::make('check_in')
//                    ->form([
//                        DatePicker::make('check_in_from'),
//                        DatePicker::make('check_in_until'),
//                    ])
//                    ->query(function (Builder $query, array $data): Builder {
//                        return $query
//                            ->when(
//                                $data['check_in_from'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('check_in', '>=', $date),
//                            )
//                            ->when(
//                                $data['check_in_until'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('check_in', '<=', $date),
//                            );
//                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->emptyStateHeading('No attendance records found')
            ->emptyStateDescription('Create a new attendance record to get started.');
            //->defaultSort('check_in', 'desc');
    }
}
