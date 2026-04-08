<?php

namespace App\Filament\Pages;

use App\Models\Teacher;
use App\Models\Timetable;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction as TablesExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class MyTimetable extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $title = 'Jadwal Saya';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.my-timetable';


    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('teacher');
    }

    public function table(Table $table): Table
    {
        $teacher = Teacher::query()->where('user_id', auth()->user()->id)->first();

        return $table
            ->paginated(false)
            ->query(Timetable::query()->where('teacher_id', $teacher->id))
            ->columns([
                TextColumn::make('day.name')
                    ->label('Hari'),
                TextColumn::make('timeslot.full_time')
                    ->label('Waktu'),
                TextColumn::make('lesson.name')
                    ->label('Mata Pelajaran'),
                TextColumn::make('classroom.name')
                    ->label('Kelas'),
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
                // Tables\Actions\CreateAction::make(),
                TablesExportAction::make() 
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withColumns([
                            Column::make('day.name')->heading('Hari')
                        ])
                        ->withFilename('Jadwal Mengajar Saya')
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                ]), 
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ]);
    }
}
