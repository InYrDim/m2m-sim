<?php

namespace App\Filament\Resources\TimeslotResource\Pages;

use App\Filament\Resources\TimeslotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTimeslot extends EditRecord
{
    protected static string $resource = TimeslotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {

        $data['full_time'] = $data['time_start'].' - '.$data['time_end'];

        $record->update($data);

        return $record;
    }
}
