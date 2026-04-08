<?php

namespace App\Filament\Resources\Friendships\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FriendshipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('requester_id')
                    ->relationship('requester', 'name')
                    ->required(),
                Select::make('addressee_id')
                    ->relationship('addressee', 'name')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
            ]);
    }
}
