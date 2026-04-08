<?php

namespace App\Filament\Resources\CommentVotes\Pages;

use App\Filament\Resources\CommentVotes\CommentVoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCommentVote extends EditRecord
{
    protected static string $resource = CommentVoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
