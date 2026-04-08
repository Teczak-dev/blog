<?php

namespace App\Filament\Resources\CommentVotes\Pages;

use App\Filament\Resources\CommentVotes\CommentVoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommentVotes extends ListRecords
{
    protected static string $resource = CommentVoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
