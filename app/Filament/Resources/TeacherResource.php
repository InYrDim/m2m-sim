<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\Teacher;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Guru';
    protected static ?string $navigationGroup = 'Pengguna';

    protected static ?string $modelLabel = 'Guru';
    protected static ?string $pluralModelLabel = 'Guru';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->columnSpanFull()
                        ->autocomplete(false),
                    TextInput::make('code')
                        ->label('Kode')
                        ->unique(ignoreRecord: true)
                        ->maxLength(2)
                        ->required()
                        ->autocomplete(false),
                    TextInput::make('nip')
                        ->label('NIP')
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->numeric()
                        ->nullable()
                        ->autocomplete(false),
                    TextInput::make('phone')
                        ->label('Telepon')
                        ->tel()
                        ->nullable()
                        ->autocomplete(false),
                    Select::make('lesson_id')
                        ->label('Mata Pelajaran')
                        ->required()
                        ->relationship('lesson', 'name')
                        ->native(false)
                        ->searchable(),
                ])
                    ->columns(2),
                Section::make()
                    ->relationship('user')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull()
                            ->autocomplete(false),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->minLength(8)
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->minLength(8)
                            ->password()
                            ->revealable(),
                    ])
                    ->columns(2)
                    ->hiddenOn('create'),
                Section::make()
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(table: User::class)
                            ->columnSpanFull()
                            ->autocomplete(false),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->minLength(8)
                            ->nullable()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->minLength(8)
                            ->password()
                            ->revealable(),
                    ])
                    ->columns(2)
                    ->hiddenOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('nip')
                    ->label('NIP'),
                TextColumn::make('phone')
                    ->label('Telepon'),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('lesson.name')
                    ->label('Mata Pelajaran')
                    ->searchable(),
            ])
            ->defaultSort('code', 'asc')
            ->filters([
                SelectFilter::make('lesson')
                    ->relationship('lesson', 'name')
                    ->label('Mata Pelajaran')
                    ->preload()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
