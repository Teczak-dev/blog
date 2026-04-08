<?php

namespace App\Mail;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPostNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $follower;

    /**
     * Create a new message instance.
     */
    public function __construct(Post $post, User $follower)
    {
        $this->post = $post;
        $this->follower = $follower;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nowy post od {$this->post->user->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-post-notification',
            with: [
                'post' => $this->post,
                'follower' => $this->follower,
                'postUrl' => url("/posts/{$this->post->id}"),
            ],
        );
    }
}
