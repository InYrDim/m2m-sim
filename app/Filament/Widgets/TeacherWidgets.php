<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use App\Models\Timetable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherWidgets extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $teacher = Teacher::query()->where('user_id', auth()->user()->id)->first();

        return [
            Stat::make('Total Kelas Ajar', Timetable::where('teacher_id', $teacher->id)->distinct()->count('classroom_id')),
            Stat::make('Total Pertemuan Per Minggu', Timetable::where('teacher_id', $teacher->id)->count()),
            Stat::make('Tanggal', date('d M Y')),
        ];
    }

    public static function canView(): bool 
    {
        return auth()->user()->hasRole('teacher');
    } 
}
