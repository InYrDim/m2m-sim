<?php

namespace App\Filament\Resources\TimeslotResource\Pages;

use App\Filament\Resources\TimeslotResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTimeslot extends CreateRecord
{
    protected static string $resource = TimeslotResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);

        $data['full_time'] = $data['time_start'].' - '.$data['time_end'];

        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
