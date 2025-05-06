<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherProfileResource\Pages;
use App\Models\TeacherProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherProfileResource extends Resource
{
    protected static ?string $model = TeacherProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'School Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->unique(ignoreRecord: true)
                    ->label('Teacher'),

                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('qualification')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('experience_years')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(50),

                Forms\Components\TextInput::make('specialization')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('bio')
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),

                Forms\Components\TextInput::make('address')
                    ->maxLength(255),

                Forms\Components\TextInput::make('emergency_contact')
                    ->maxLength(255),

                Forms\Components\TagsInput::make('certifications')
                    ->columnSpanFull(),

                Forms\Components\TagsInput::make('skills')
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('profile_photo')
                    ->image()
                    ->directory('teacher-profiles')
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('joining_date')
                    ->required(),

                Forms\Components\DatePicker::make('contract_end_date'),

                Forms\Components\Select::make('employment_status')
                    ->options([
                        'active' => 'Active',
                        'on_leave' => 'On Leave',
                        'terminated' => 'Terminated',
                    ])
                    ->required()
                    ->default('active'),

                Forms\Components\TextInput::make('salary')
                    ->numeric()
                    ->prefix('$')
                    ->maxValue(1000000),

                Forms\Components\KeyValue::make('working_hours')
                    ->keyLabel('Day')
                    ->valueLabel('Hours')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Teacher'),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('qualification')
                    ->searchable(),

                Tables\Columns\TextColumn::make('experience_years')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'on_leave' => 'warning',
                        'terminated' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('joining_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract_end_date')
                    ->date()
                    ->sortable(),

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
                Tables\Filters\SelectFilter::make('employment_status')
                    ->options([
                        'active' => 'Active',
                        'on_leave' => 'On Leave',
                        'terminated' => 'Terminated',
                    ]),
                Tables\Filters\Filter::make('contract_expiring_soon')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('contract_end_date')
                        ->where('contract_end_date', '<=', now()->addMonths(3))
                        ->where('contract_end_date', '>', now())),
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
            'index' => Pages\ListTeacherProfiles::route('/'),
            'create' => Pages\CreateTeacherProfile::route('/create'),
            'view' => Pages\ViewTeacherProfile::route('/{record}'),
            'edit' => Pages\EditTeacherProfile::route('/{record}/edit'),
        ];
    }
} 