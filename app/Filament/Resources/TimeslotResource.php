<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeslotResource\Pages;
use App\Filament\Resources\TimeslotResource\RelationManagers;
use App\Models\Timeslot;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimeslotResource extends Resource
{
    protected static ?string $model = Timeslot::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Waktu';
    protected static ?string $navigationGroup = 'Penjadwalan';
    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Waktu';
    protected static ?string $pluralModelLabel = 'Waktu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TimePicker::make('time_start')
                        ->label(' Waktu Mulai')
                        ->seconds(false),
                    TimePicker::make('time_end')
                        ->label(' Waktu Selesai')
                        ->seconds(false)
                        ->after('time_start')
                        ->validationMessages([
                            'after' => 'Waktu selesai harus berisi waktu setelah waktu mulai',
                        ]),
                ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('time_start')
                    ->label('Waktu Mulai')
                    ->dateTime('H:i')
                    ->searchable(),
                TextColumn::make('time_end')
                    ->dateTime('H:i')
                    ->label('Waktu Selesai')
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimeslots::route('/'),
            'create' => Pages\CreateTimeslot::route('/create'),
            'edit' => Pages\EditTimeslot::route('/{record}/edit'),
        ];
    }
}
