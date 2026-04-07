<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Post;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Zdjęcie')
                    ->disk('public')
                    ->size(40)
                    ->circular(),

                TextColumn::make('title')
                    ->label('Tytuł')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                BadgeColumn::make('category')
                    ->label('Kategoria')
                    ->getStateUsing(fn ($record) => $record->category)
                    ->color(fn ($record) => match($record->category_color ?? 'blue') {
                        'blue' => 'info',
                        'green' => 'success',
                        'red' => 'danger',
                        'yellow' => 'warning',
                        'purple' => 'primary',
                        'indigo' => 'info',
                        'pink' => 'primary',
                        'gray' => 'gray',
                        default => 'info'
                    })
                    ->searchable(),
                
                TextColumn::make('tags')
                    ->label('Tagi')
                    ->badge()
                    ->separator(',')
                    ->limit(3)
                    ->limitList(3)
                    ->expandableLimitedList(),
                
                TextColumn::make('read_time_minutes')
                    ->label('Czas czytania')
                    ->suffix(' min')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('author')
                    ->label('Autor')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                
                TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->sortable()
                    ->toggleable(),
                
                IconColumn::make('is_published')
                    ->label('Opublikowany')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Utworzony')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Zaktualizowany')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_color')
                    ->label('Kolor kategorii')
                    ->options(Post::getCategoryColors())
                    ->multiple(),
                    
                SelectFilter::make('is_published')
                    ->label('Status publikacji')
                    ->options([
                        '1' => 'Opublikowany',
                        '0' => 'Szkic',
                    ]),
                    
                SelectFilter::make('user')
                    ->label('Użytkownik')
                    ->relationship('user', 'name')
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
