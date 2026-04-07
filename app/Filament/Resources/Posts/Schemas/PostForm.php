<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('photo')
                    ->label('Zdjęcie główne')
                    ->image()
                    ->disk('public')
                    ->directory('posts')
                    ->imageEditor()
                    ->columnSpanFull(),
                
                TextInput::make('title')
                    ->label('Tytuł')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('category')
                    ->label('Kategoria')
                    ->helperText('Zostanie wygenerowana z tytułu jeśli pozostawisz puste')
                    ->maxLength(255),
                
                Select::make('category_color')
                    ->label('Kolor kategorii')
                    ->options(Post::getCategoryColors())
                    ->default('blue')
                    ->required(),
                
                Textarea::make('lead')
                    ->label('Krótki opis')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),
                
                RichEditor::make('content')
                    ->label('Treść postu')
                    ->required()
                    ->columnSpanFull(),
                
                TagsInput::make('tags')
                    ->label('Tagi/Hashtagi')
                    ->placeholder('Dodaj tagi...')
                    ->helperText('Naciśnij Enter aby dodać tag'),
                
                TextInput::make('read_time_minutes')
                    ->label('Czas czytania (minuty)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(60)
                    ->helperText('Zostanie automatycznie wyliczony jeśli pozostawisz puste'),
                
                TextInput::make('author')
                    ->label('Autor')
                    ->required()
                    ->maxLength(255),
                
                Toggle::make('is_published')
                    ->label('Opublikowany')
                    ->default(true),
            ]);
    }
}
