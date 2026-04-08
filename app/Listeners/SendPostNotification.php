<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Mail\NewPostNotification;
use Illuminate\Support\Facades\Mail;

class SendPostNotification
{
    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $post = $event->post;
        $author = $post->user;

        // Get all followers who:
        // 1. Have new post notifications enabled
        // 2. Have verified email
        // 3. Haven't muted the author
        $followers = $author->followers()
                           ->where('notify_new_posts', true)
                           ->whereNotNull('email_verified_at')
                           ->get()
                           ->filter(function ($follower) use ($author) {
                               return !$follower->hasMuted($author);
                           });

        foreach ($followers as $follower) {
            Mail::to($follower->email)->send(
                new NewPostNotification($post, $follower)
            );
        }
    }
}
