<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('post.title')
                    ->label('Post')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('author_display_name')
                    ->label('Autor')
                    ->getStateUsing(function ($record) {
                        return $record->user ? $record->user->name : $record->author_name;
                    })
                    ->searchable(['author_name', 'users.name']),
                TextColumn::make('content')
                    ->label('Komentarz')
                    ->limit(50)
                    ->searchable(),
                IconColumn::make('is_approved')
                    ->label('Zatwierdzony')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('user_type')
                    ->label('Typ użytkownika')
                    ->getStateUsing(function ($record) {
                        return $record->user_id ? 'Zalogowany' : 'Gość';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Zalogowany' => 'success',
                        'Gość' => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('approvedBy.name')
                    ->label('Zatwierdził')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('is_approved')
                    ->label('Status')
                    ->options([
                        '1' => 'Zatwierdzone',
                        '0' => 'Oczekujące',
                    ]),
                SelectFilter::make('user_type')
                    ->label('Typ użytkownika')
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'logged' => $query->whereNotNull('user_id'),
                            'guest' => $query->whereNull('user_id'),
                            default => $query,
                        };
                    })
                    ->options([
                        'logged' => 'Zalogowani',
                        'guest' => 'Goście',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Zatwierdź')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_approved)
                    ->action(function ($record) {
                        $record->update([
                            'is_approved' => true,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),
                Action::make('reject')
                    ->label('Odrzuć')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->is_approved)
                    ->action(function ($record) {
                        $record->update([
                            'is_approved' => false,
                            'approved_by' => null,
                            'approved_at' => null,
                        ]);
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve_selected')
                        ->label('Zatwierdź zaznaczone')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_approved' => true,
                                    'approved_by' => auth()->id(),
                                    'approved_at' => now(),
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
