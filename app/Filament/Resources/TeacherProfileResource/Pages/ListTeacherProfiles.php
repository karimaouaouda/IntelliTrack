<?php

namespace App\Filament\Resources\TeacherProfileResource\Pages;

use App\Filament\Resources\TeacherProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ListTeacherProfiles extends ListRecords
{
    protected static string $resource = TeacherProfileResource::class;

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
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Teacher'),

                TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('qualification')
                    ->searchable(),

                TextColumn::make('experience_years')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('employment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'on_leave' => 'warning',
                        'terminated' => 'danger',
                    }),

                TextColumn::make('joining_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('contract_end_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('employment_status')
                    ->options([
                        'active' => 'Active',
                        'on_leave' => 'On Leave',
                        'terminated' => 'Terminated',
                    ]),
                Filter::make('contract_expiring_soon')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('contract_end_date')
                        ->where('contract_end_date', '<=', now()->addMonths(3))
                        ->where('contract_end_date', '>', now())),
                Filter::make('experienced_teachers')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('experience_years', '>=', 5)),
            ])
            ->actions([
                ViewAction::make(),
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
            ->emptyStateHeading('No teacher profiles found')
            ->emptyStateDescription('Create a new teacher profile to get started.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}
