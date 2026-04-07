<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Schemas\Schema;

class CommentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Szczegóły komentarza')
                    ->schema([
                        TextEntry::make('post.title')
                            ->label('Post')
                            ->url(fn ($record) => route('posts.show', $record->post_id))
                            ->openUrlInNewTab(),
                        TextEntry::make('author_display_name')
                            ->label('Autor')
                            ->getStateUsing(function ($record) {
                                return $record->user ? $record->user->name : $record->author_name;
                            }),
                        TextEntry::make('author_display_email')
                            ->label('Email')
                            ->getStateUsing(function ($record) {
                                return $record->user ? $record->user->email : $record->author_email;
                            }),
                        TextEntry::make('user_type')
                            ->label('Typ użytkownika')
                            ->getStateUsing(function ($record) {
                                return $record->user_id ? 'Zalogowany użytkownik' : 'Gość';
                            })
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Zalogowany użytkownik' => 'success',
                                'Gość' => 'warning',
                            }),
                        TextEntry::make('content')
                            ->label('Treść komentarza')
                            ->columnSpanFull()
                            ->prose(),
                    ])->columns(2),
                
                Section::make('Informacje o moderacji')
                    ->schema([
                        IconEntry::make('is_approved')
                            ->label('Status zatwierdzenia')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        TextEntry::make('approvedBy.name')
                            ->label('Zatwierdził')
                            ->placeholder('—'),
                        TextEntry::make('approved_at')
                            ->label('Data zatwierdzenia')
                            ->dateTime('d.m.Y H:i')
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->label('Data utworzenia')
                            ->dateTime('d.m.Y H:i'),
                    ])->columns(2),
            ]);
    }
}
