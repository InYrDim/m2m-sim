<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page implements HasForms
{
    protected static ?string $title = 'Profil';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.edit-profile';

    use InteractsWithForms;

    // Form field properties
    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;
    public ?string $nip = null;
    public ?string $phone = null;
    // public ?string $address = null;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'teacher']);
    }

    public User $user;

    public function mount(): void
    {
        /** @var \App\Models\User */
        $activeUser = auth()->user();

        // dd($activeUser);
        if ($activeUser->hasRole('teacher')) {
            $this->form->fill([
                'name' => $activeUser->name,
                'email' => $activeUser->email,
                /* Extra Informations */
                'nip' => $activeUser->teachers->nip,
                'phone' => $activeUser->teachers->phone,
                // 'address' => $activeUser->teachers->address,
            ]);
        } else {
            $this->form->fill([
                'name' => $activeUser->name,
                'email' => $activeUser->email,
            ]);
        }
        
    }

    public function form(Form $form): Form
    {
        $administrator = Auth::user()->hasAnyRole(['super_admin', 'admin']);
        $teacher = Auth::user()->hasRole('teacher');
        $columnSpan = 2;

        if ($teacher) {
            $columnSpan = 1;
        }

        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->visible($administrator)
                        ->columnSpanFull(),
                    TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->visible($teacher),
                        // ->columnSpanFull(),
                    TextInput::make('nip')
                        ->label('NIP')
                        ->nullable()
                        ->visible($teacher),
                    TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->tel()
                        ->nullable()
                        ->visible($teacher),
                    // TextInput::make('address')
                    //     ->label('Alamat')
                    //     ->required()
                    //     ->visible($teacher),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->columnSpan($columnSpan),
                ])
                    ->columns(2),
                Section::make()->schema([
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->confirmed()
                        ->minLength(8)
                        ->nullable(),
                    TextInput::make('password_confirmation')
                        ->label('Konfirmasi Password')
                        ->minLength(8)
                        ->password()
                        ->revealable(),
                ])
                    ->columns(2),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            // dd($data);
            $state = array_filter([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? Hash::make($this->password) : null,
                /* Extra Informations */
                'nip' => $this->nip,
                'phone' => $this->phone,
                // 'address' => $this->address,
            ]);
            
            /** @var \App\Models\User */
            $user = Auth::user();
            $user->update($state);

            if ($user->hasRole('teacher')) {
                $teacher = $user->teachers;
                $teacher->update($state);
            }
            
            $this->reset(['password', 'password_confirmation']);

            Notification::make()
                ->success()
                ->title('Profil berhasil diperbarui')
                ->send();
            
        } catch (Halt $exception) {
            Notification::make()
                ->danger()
                ->title('Terjadi kesalahan!')
                ->send();
            return;
        }
    }
}
