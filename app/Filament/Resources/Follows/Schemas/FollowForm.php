<?php

namespace App\Filament\Resources\Follows\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class FollowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('follower_id')
                    ->relationship('follower', 'name')
                    ->required(),
                Select::make('followed_id')
                    ->relationship('followed', 'name')
                    ->required(),
            ]);
    }
}
