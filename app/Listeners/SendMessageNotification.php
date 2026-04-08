<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Mail\NewMessageNotification;
use Illuminate\Support\Facades\Mail;

class SendMessageNotification
{
    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;
        $sender = $message->user;

        // Get other participants who should be notified
        $recipients = $conversation->participants()
                                  ->where('users.id', '!=', $sender->id) // Not the sender
                                  ->whereNull('conversation_participants.left_at') // Active participants
                                  ->where('notify_messages', true) // Have message notifications enabled
                                  ->whereNotNull('email_verified_at') // Verified email
                                  ->get()
                                  ->filter(function ($participant) use ($sender) {
                                      return !$participant->hasMuted($sender);
                                  });

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->send(
                new NewMessageNotification($message, $recipient)
            );
        }
    }
}
