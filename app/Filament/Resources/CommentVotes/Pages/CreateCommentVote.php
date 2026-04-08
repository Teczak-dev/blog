<?php

namespace App\Filament\Resources\CommentVotes\Pages;

use App\Filament\Resources\CommentVotes\CommentVoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCommentVote extends CreateRecord
{
    protected static string $resource = CommentVoteResource::class;
}
