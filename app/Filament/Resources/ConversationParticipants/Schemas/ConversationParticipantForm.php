<?php

namespace App\Filament\Resources\ConversationParticipants\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConversationParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('conversation_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('joined_at')
                    ->required(),
                DateTimePicker::make('left_at'),
                DateTimePicker::make('last_read_at'),
            ]);
    }
}
