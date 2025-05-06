<?php

namespace App\Filament\Resources\UserProfileResource\Pages;

use App\Filament\Resources\UserProfileResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class ListUserProfiles extends ListRecords
{
    protected static string $resource = UserProfileResource::class;

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
                ImageColumn::make('teacherProfile.profile_photo')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => "https://ui-avatars.com/api/?name={$record->name}&background=random")
                    ->toggleable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ref_id')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('roles.name')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('teacherProfile.subject')
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('teacherProfile.qualification')
                    ->label('Qualification')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('teacherProfile.experience_years')
                    ->label('Experience')
                    ->suffix(' years')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('teacherProfile.employment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'on_leave' => 'warning',
                        'terminated' => 'danger',
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('teacherProfile.joining_date')
                    ->label('Joined')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('teacherProfile.contract_end_date')
                    ->label('Contract Ends')
                    ->date()
                    ->sortable()
                    ->toggleable(),

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
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                TernaryFilter::make('has_teacher_profile')
                    ->label('Has Teacher Profile')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('teacherProfile'),
                        false: fn (Builder $query) => $query->whereDoesntHave('teacherProfile'),
                    ),

                SelectFilter::make('teacherProfile.employment_status')
                    ->label('Employment Status')
                    ->options([
                        'active' => 'Active',
                        'on_leave' => 'On Leave',
                        'terminated' => 'Terminated',
                    ]),

                Filter::make('contract_expiring_soon')
                    ->label('Contract Expiring Soon')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereHas('teacherProfile', function ($query) {
                            $query->whereNotNull('contract_end_date')
                                ->where('contract_end_date', '<=', now()->addMonths(3))
                                ->where('contract_end_date', '>', now());
                        })),

                Filter::make('experienced_teachers')
                    ->label('Experienced Teachers')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereHas('teacherProfile', function ($query) {
                            $query->where('experience_years', '>=', 5);
                        })),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Created from'),
                        DatePicker::make('created_until')
                            ->label('Created until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record) {
                        // Add any pre-deletion logic here
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Add any pre-bulk-deletion logic here
                        }),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->emptyStateHeading('No users found')
            ->emptyStateDescription('Create a new user to get started.')
            ->emptyStateIcon('heroicon-o-users')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
