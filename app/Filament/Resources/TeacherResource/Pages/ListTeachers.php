<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use App\Services\TeacherImportService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('template_csv')
                ->label('Template CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function (TeacherImportService $service) {
                    return response()->streamDownload(function () use ($service) {
                        echo $service->getTemplateContent();
                    }, 'teacher_template.csv');
                }),
            Actions\Action::make('import_csv')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    Forms\Components\FileUpload::make('csv_file')
                        ->label('Pilih File CSV')
                        ->required()
                        ->disk('local')
                        ->directory('temp-imports')
                        ->acceptedFileTypes(['text/csv', 'application/csv']),
                ])
                ->action(function (array $data, TeacherImportService $service) {
                    $filePath = storage_path('app/' . $data['csv_file']);
                    $result = $service->import($filePath);

                    if ($result['success'] > 0) {
                        Notification::make()
                            ->title('Import Berhasil')
                            ->body($result['success'] . ' guru telah diimport/diupdate.')
                            ->success()
                            ->send();
                    }

                    if (!empty($result['errors'])) {
                        foreach ($result['errors'] as $error) {
                            Notification::make()
                                ->title('Import Error')
                                ->body($error)
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
