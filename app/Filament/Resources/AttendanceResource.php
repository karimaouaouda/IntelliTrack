<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'School Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('attendable_type')
                    ->label('Type')
                    ->options([
                        'App\Models\User' => 'Teacher',
                        'App\Models\Student' => 'Student',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('attendable_id', null)),

                Forms\Components\Select::make('attendable_id')
                    ->label('Person')
                    ->searchable()
                    ->preload()
                    ->options(function (Forms\Get $get) {
                        $type = $get('attendable_type');
                        if (!$type) return [];

                        return match ($type) {
                            'App\Models\User' => \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))
                                ->pluck('name', 'id'),
                            'App\Models\Student' => \App\Models\Student::pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->required(),

                Forms\Components\Select::make('classroom_id')
                    ->relationship('classroom', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('device_id')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('check_in')
                    ->required()
                    ->default(now()),

                Forms\Components\DateTimePicker::make('check_out')
                    ->nullable()
                    ->after('check_in'),

                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attendable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'App\Models\User' => 'Teacher',
                        'App\Models\Student' => 'Student',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendable.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('classroom.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('device_id')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_out')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

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
                Tables\Filters\SelectFilter::make('attendable_type')
                    ->label('Type')
                    ->options([
                        'App\Models\User' => 'Teacher',
                        'App\Models\Student' => 'Student',
                    ]),

                Tables\Filters\SelectFilter::make('classroom')
                    ->relationship('classroom', 'name'),

                Tables\Filters\Filter::make('check_in')
                    ->form([
                        Forms\Components\DatePicker::make('check_in_from'),
                        Forms\Components\DatePicker::make('check_in_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['check_in_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('check_in', '>=', $date),
                            )
                            ->when(
                                $data['check_in_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('check_in', '<=', $date),
                            );
                    }),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['attendable', 'classroom']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['device_id', 'attendable.name', 'classroom.name'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->attendable->name . ' - ' . $record->classroom->name;
    }
} 