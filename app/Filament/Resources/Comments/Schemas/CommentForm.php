<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informacje o komentarzu')
                    ->schema([
                        Select::make('post_id')
                            ->label('Post')
                            ->relationship('post', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        TextInput::make('author_name')
                            ->label('Imię i nazwisko autora')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('author_email')
                            ->label('Adres e-mail autora')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ]),
                    
                Section::make('Treść komentarza')
                    ->schema([
                        Textarea::make('content')
                            ->label('Treść')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
