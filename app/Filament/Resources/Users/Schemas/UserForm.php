<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informacje o użytkowniku')
                    ->schema([
                        TextInput::make('name')
                            ->label('Imię i nazwisko')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('email')
                            ->label('Adres e-mail')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                    ]),
                    
                Section::make('Bezpieczeństwo')
                    ->schema([
                        TextInput::make('password')
                            ->label('Hasło')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('Pozostaw puste aby nie zmieniać hasła'),
                            
                        DateTimePicker::make('email_verified_at')
                            ->label('E-mail zweryfikowany')
                            ->helperText('Data i czas weryfikacji adresu e-mail'),
                    ]),
            ]);
    }
}
