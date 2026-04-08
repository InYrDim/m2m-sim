<?php

namespace App\Filament\Resources\ClassroomResource\Pages;

use App\Filament\Resources\ClassroomResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateClassroom extends CreateRecord
{
    protected static string $resource = ClassroomResource::class;

    // protected function handleRecordCreation(array $data): Model
    // {
    //     // dd($data);
    //     $data['name'] = strtoupper($data['level'].' '.$data['name']);

    //     return static::getModel()::create($data);
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
