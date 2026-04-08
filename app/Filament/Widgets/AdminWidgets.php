<?php

namespace App\Filament\Widgets;

use App\Models\Classroom;
use App\Models\Lesson;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminWidgets extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Kelas', Classroom::count()),
            Stat::make('Total Guru', Teacher::count()),
            Stat::make('Total Mata Pelajaran', Lesson::count()),
        ];
    }

    public static function canView(): bool 
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    } 
}
