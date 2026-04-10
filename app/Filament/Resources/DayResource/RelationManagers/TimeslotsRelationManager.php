<?php

namespace App\Filament\Resources\DayResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimeslotsRelationManager extends RelationManager
{
    protected static string $relationship = 'timeslots';
    protected static ?string $title = 'Slot Waktu';
    protected static ?string $modelLabel = 'Waktu';
    protected static ?string $pluralModelLabel = 'Waktu';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('jam_ke')
                    ->label('Jam Ke')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_time')
            ->columns([
                Tables\Columns\TextColumn::make('jam_ke')
                    ->label('Jam Ke')
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_time')
                    ->label('Waktu'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('jam_ke')
                            ->label('Jam Ke')
                            ->numeric()
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
