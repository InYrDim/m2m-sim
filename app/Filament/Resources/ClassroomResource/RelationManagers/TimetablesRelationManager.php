<?php

namespace App\Filament\Resources\ClassroomResource\RelationManagers;

use App\Models\Day;
use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimetablesRelationManager extends RelationManager
{
    protected static string $relationship = 'timetables';
    protected static ?string $title = 'Jadwal Pelajaran';
    protected static ?string $modelLabel = 'Jadwal';
    protected static ?string $pluralModelLabel = 'Jadwal';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
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
                ]),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('lesson_id')
                        ->label('Mata Pelajaran')
                        ->required()
                        ->relationship('lesson', 'name')
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('teacher_id', null))
                        ->searchable(),
                    Forms\Components\Select::make('teacher_id')
                        ->label('Guru')
                        ->native(false)
                        ->options(function (callable $get) {
                            $lesson = Lesson::find($get('lesson_id'));
                            if (!$lesson) {
                                return [];
                            }

                            return $lesson->teachers->pluck('name', 'id');
                        })
                        ->visible(fn (Get $get) => $get('lesson_id') != null)
                        ->searchable(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->recordTitleAttribute('timeslot.full_time')
            ->columns([
                TextColumn::make('timeslot.full_time')
                    ->label('Waktu'),
                TextColumn::make('lesson.name')
                    ->label('Mata Pelajaran'),
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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
