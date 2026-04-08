<?php

namespace App\Filament\Resources\CommentVotes;

use App\Filament\Resources\CommentVotes\Pages\CreateCommentVote;
use App\Filament\Resources\CommentVotes\Pages\EditCommentVote;
use App\Filament\Resources\CommentVotes\Pages\ListCommentVotes;
use App\Filament\Resources\CommentVotes\Schemas\CommentVoteForm;
use App\Filament\Resources\CommentVotes\Tables\CommentVotesTable;
use App\Models\CommentVote;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommentVoteResource extends Resource
{
    protected static ?string $model = CommentVote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CommentVoteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommentVotesTable::configure($table);
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
            'index' => ListCommentVotes::route('/'),
            'create' => CreateCommentVote::route('/create'),
            'edit' => EditCommentVote::route('/{record}/edit'),
        ];
    }
}
