<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserProfileResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserProfileResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->maxLength(255)
                    ->hiddenOn('edit'),

                Forms\Components\TextInput::make('ref_id')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload(),

                Forms\Components\Section::make('Teacher Profile')
                    ->schema([
                        Forms\Components\TextInput::make('teacherProfile.subject')
                            ->label('Subject')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('teacherProfile.qualification')
                            ->label('Qualification')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('teacherProfile.experience_years')
                            ->label('Experience Years')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(50),

                        Forms\Components\TextInput::make('teacherProfile.specialization')
                            ->label('Specialization')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('teacherProfile.bio')
                            ->label('Bio')
                            ->maxLength(1000),

                        Forms\Components\TextInput::make('teacherProfile.phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('teacherProfile.address')
                            ->label('Address')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('teacherProfile.emergency_contact')
                            ->label('Emergency Contact')
                            ->maxLength(255),

                        Forms\Components\TagsInput::make('teacherProfile.certifications')
                            ->label('Certifications'),

                        Forms\Components\TagsInput::make('teacherProfile.skills')
                            ->label('Skills'),

                        Forms\Components\FileUpload::make('teacherProfile.profile_photo')
                            ->label('Profile Photo')
                            ->image()
                            ->directory('teacher-profiles'),

                        Forms\Components\DatePicker::make('teacherProfile.joining_date')
                            ->label('Joining Date'),

                        Forms\Components\DatePicker::make('teacherProfile.contract_end_date')
                            ->label('Contract End Date'),

                        Forms\Components\Select::make('teacherProfile.employment_status')
                            ->label('Employment Status')
                            ->options([
                                'active' => 'Active',
                                'on_leave' => 'On Leave',
                                'terminated' => 'Terminated',
                            ]),

                        Forms\Components\TextInput::make('teacherProfile.salary')
                            ->label('Salary')
                            ->numeric()
                            ->prefix('$')
                            ->maxValue(1000000),

                        Forms\Components\KeyValue::make('teacherProfile.working_hours')
                            ->label('Working Hours')
                            ->keyLabel('Day')
                            ->valueLabel('Hours'),

                        Forms\Components\Textarea::make('teacherProfile.notes')
                            ->label('Notes')
                            ->maxLength(1000),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($record) => $record?->isTeacher()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ref_id')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge(),

                Tables\Columns\TextColumn::make('teacherProfile.subject')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('teacherProfile.employment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'on_leave' => 'warning',
                        'terminated' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('has_teacher_profile')
                    ->query(fn (Builder $query): Builder => $query->whereHas('teacherProfile')),

                Tables\Filters\SelectFilter::make('teacherProfile.employment_status')
                    ->label('Employment Status')
                    ->options([
                        'active' => 'Active',
                        'on_leave' => 'On Leave',
                        'terminated' => 'Terminated',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserProfiles::route('/'),
            'create' => Pages\CreateUserProfile::route('/create'),
            'view' => Pages\ViewUserProfile::route('/{record}'),
            'edit' => Pages\EditUserProfile::route('/{record}/edit'),
        ];
    }
} 