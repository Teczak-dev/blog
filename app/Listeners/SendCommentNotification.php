<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Mail\NewCommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendCommentNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;
        $post = $comment->post;
        $postAuthor = $post->user;

        // Don't send notification if:
        // 1. Post author has notifications disabled
        // 2. Comment author is the same as post author (self-comment)
        // 3. Post author hasn't verified their email
        if (
            !$postAuthor->email_notifications ||
            !$postAuthor->hasVerifiedEmail() ||
            ($comment->user_id && $comment->user_id === $postAuthor->id)
        ) {
            return;
        }

        // Send the notification email
        Mail::to($postAuthor->email)->send(
            new NewCommentNotification($comment, $post, $postAuthor)
        );
    }
}
