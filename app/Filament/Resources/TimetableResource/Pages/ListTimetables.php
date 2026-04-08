<?php

namespace App\Filament\Resources\TimetableResource\Pages;

use App\Filament\Resources\TimetableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListTimetables extends ListRecords
{
    protected static string $resource = TimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make() 
                ->exports([
                    ExcelExport::make()
                        // ->withColumns([
                        //     Column::make('day.name')->heading('Hari')
                        // ])
                        ->fromTable()
                        ->withFilename(fn ($resource) => $resource::getModelLabel())
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                ]), 
            ];
    }
}
