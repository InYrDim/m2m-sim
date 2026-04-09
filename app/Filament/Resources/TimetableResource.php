<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimetableResource\Pages;
use App\Filament\Resources\TimetableResource\RelationManagers;
use App\Models\Timetable;
use App\Models\Day;
use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimetableResource extends Resource
{
    protected static ?string $model = Timetable::class;
    // protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static ?string $navigationLabel = 'Jadwal Pembelajaran';
    protected static ?string $navigationGroup = 'Penjadwalan';
    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Jadwal Pembelajaran';
    protected static ?string $pluralModelLabel = 'Jadwal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('classroom_id')
                        ->label('Kelas')
                        ->relationship('classroom', 'name')
                        ->required()
                        ->native(false)
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('day_id')
                        ->label('Hari')
                        ->required()
                        ->relationship('day', 'name', fn (Builder $query) => $query->orderBy('id', 'asc'))
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('timeslot_id', null)),
                    Forms\Components\Select::make('timeslot_id')
                        ->label('Waktu')
                        ->required()
                        ->native(false)
                        ->options(function (callable $get) {
                            $day = Day::find($get('day_id'));
                            if (!$day) {
                                return [];
                            }

                            return $day->timeslots->pluck('full_time', 'id');
                        })
                        ->visible(fn (Get $get) => $get('day_id') != null),
                ])->columns(3),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('lesson_id')
                        ->label('Mata Pelajaran')
                        ->required()
                        ->relationship('lesson', 'name')
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('teacher_code', null))
                        ->searchable(),
                    Forms\Components\Select::make('teacher_code')
                        ->label('Guru')
                        ->native(false)
                        ->options(function (callable $get) {
                            $lesson = Lesson::find($get('lesson_id'));
                            if (!$lesson) {
                                return [];
                            }

                            return $lesson->teachers->pluck('name', 'code');
                        })
                        ->visible(fn (Get $get) => $get('lesson_id') != null)
                        ->searchable(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('day.name')
                    ->label('Hari'),
                    // ->visible(false),
                TextColumn::make('timeslot.full_time')
                    ->label('Waktu')
                    ->searchable(),
                TextColumn::make('lesson.name')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                TextColumn::make('teacher.name')
                    ->label('Guru'),
            ])
            ->groups([
                Group::make('day.name')
                    ->label('Hari')
                    ->titlePrefixedWithLabel(false)

                    ->orderQueryUsing(
                        fn (Builder $query, string $direction) => $query->orderBy('id', 'asc')
                    )
            ])
            ->groupingSettingsHidden()
            ->defaultGroup('day.name')
            ->filters([
                SelectFilter::make('classroom')
                    ->relationship('classroom', 'name')
                    ->default(1)
                    ->label('Kelas')
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->bulkActions([
                // ExportBulkAction::make() 
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
            'index' => Pages\ListTimetables::route('/'),
            'create' => Pages\CreateTimetable::route('/create'),
            'edit' => Pages\EditTimetable::route('/{record}/edit'),
        ];
    }
}
