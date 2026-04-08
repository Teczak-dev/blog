<?php

namespace App\Filament\Resources\ConversationParticipants;

use App\Filament\Resources\ConversationParticipants\Pages\CreateConversationParticipant;
use App\Filament\Resources\ConversationParticipants\Pages\EditConversationParticipant;
use App\Filament\Resources\ConversationParticipants\Pages\ListConversationParticipants;
use App\Filament\Resources\ConversationParticipants\Schemas\ConversationParticipantForm;
use App\Filament\Resources\ConversationParticipants\Tables\ConversationParticipantsTable;
use App\Models\ConversationParticipant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConversationParticipantResource extends Resource
{
    protected static ?string $model = ConversationParticipant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ConversationParticipantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConversationParticipantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConversationParticipants::route('/'),
            'create' => CreateConversationParticipant::route('/create'),
            'edit' => EditConversationParticipant::route('/{record}/edit'),
        ];
    }
}
