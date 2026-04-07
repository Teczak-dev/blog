<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Imię i nazwisko')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                TextColumn::make('email')
                    ->label('Adres e-mail')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Adres e-mail skopiowany!'),
                    
                IconColumn::make('email_verified_at')
                    ->label('Zweryfikowany')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at))
                    ->sortable(),
                    
                BadgeColumn::make('posts_count')
                    ->label('Liczba postów')
                    ->counts('posts')
                    ->color('success')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Data rejestracji')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->created_at->diffForHumans()),
                    
                TextColumn::make('updated_at')
                    ->label('Ostatnia aktywność')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('verified')
                    ->label('Zweryfikowani użytkownicy')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                    
                Filter::make('unverified')
                    ->label('Niezweryfikowani użytkownicy')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
                    
                Filter::make('has_posts')
                    ->label('Użytkownicy z postami')
                    ->query(fn (Builder $query): Builder => $query->has('posts')),
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
