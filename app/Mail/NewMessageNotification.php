<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message, User $recipient)
    {
        $this->message = $message;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nowa wiadomość od {$this->message->user->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-message-notification',
            with: [
                'message' => $this->message,
                'recipient' => $this->recipient,
                'conversationUrl' => url("/conversations/{$this->message->conversation_id}"),
            ],
        );
    }
}
