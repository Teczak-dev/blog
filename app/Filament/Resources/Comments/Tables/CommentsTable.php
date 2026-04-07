<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('post.title')
                    ->label('Post')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->post->title),
                    
                TextColumn::make('author_name')
                    ->label('Autor')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                TextColumn::make('author_email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('E-mail skopiowany!')
                    ->toggleable(),
                    
                TextColumn::make('content')
                    ->label('Treść')
                    ->limit(100)
                    ->tooltip(fn ($record) => $record->content)
                    ->searchable(),
                    
                TextColumn::make('post.category')
                    ->label('Kategoria postu')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Data utworzenia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->created_at->diffForHumans()),
                    
                TextColumn::make('updated_at')
                    ->label('Ostatnia edycja')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('post')
                    ->label('Post')
                    ->relationship('post', 'title')
                    ->searchable()
                    ->multiple(),
                    
                SelectFilter::make('recent')
                    ->label('Ostatnie komentarze')
                    ->options([
                        'today' => 'Dzisiaj',
                        'week' => 'Ten tydzień',
                        'month' => 'Ten miesiąc',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value'] ?? null) {
                            'today' => $query->whereDate('created_at', today()),
                            'week' => $query->where('created_at', '>=', now()->subWeek()),
                            'month' => $query->where('created_at', '>=', now()->subMonth()),
                            default => $query,
                        };
                    }),
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
