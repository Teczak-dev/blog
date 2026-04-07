<?php

namespace App\Filament\Resources\Posts\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('author_name')
                    ->label('Autor')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('author_email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255),
                    
                Textarea::make('content')
                    ->label('Treść komentarza')
                    ->required()
                    ->rows(4)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Autor')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('author_email')
                    ->label('E-mail')
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('content')
                    ->label('Treść')
                    ->limit(100)
                    ->tooltip(fn ($record) => $record->content),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}