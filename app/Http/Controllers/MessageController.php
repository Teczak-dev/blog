<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class MessageController extends Controller
{
    /**
     * Get messages from a conversation
     */
    public function index(Request $request, Conversation $conversation): JsonResponse
    {
        $user = Auth::user();
        
        if (!$conversation->hasParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz dostępu do tej konwersacji'
            ], 403);
        }

        $messages = $conversation->messages()
                                ->with('user:id,name')
                                ->orderBy('created_at', 'desc')
                                ->paginate(50);

        // Mark conversation as read when loading messages
        $conversation->markAsReadForUser($user);

        return response()->json([
            'messages' => $messages->items(),
            'has_more' => $messages->hasMorePages(),
            'current_page' => $messages->currentPage(),
        ]);
    }

    /**
     * Send a new message
     */
    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user = Auth::user();
        
        if (!$conversation->hasParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz dostępu do tej konwersacji'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'in:text,image,file',
        ]);

        $messageType = $request->type ?? 'text';

        $recentDuplicate = Message::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->where('content', $request->content)
            ->where('type', $messageType)
            ->where('created_at', '>=', now()->subSeconds(3))
            ->latest('id')
            ->first();

        if ($recentDuplicate) {
            $recentDuplicate->loadMissing('user:id,name');

            return response()->json([
                'success' => true,
                'message' => $recentDuplicate,
                'deduplicated' => true,
            ]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'type' => $messageType,
        ]);

        $message->load('user:id,name');

        // Dispatch event for message notifications
        MessageSent::dispatch($message);
        
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }

    /**
     * Update message (edit)
     */
    public function update(Request $request, Message $message): JsonResponse
    {
        $user = Auth::user();
        
        if ($message->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Możesz edytować tylko swoje wiadomości'
            ], 403);
        }

        // Only allow editing within 15 minutes
        if ($message->created_at->diffInMinutes(now()) > 15) {
            return response()->json([
                'success' => false,
                'message' => 'Możesz edytować wiadomość tylko przez 15 minut od wysłania'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $message->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Delete message
     */
    public function destroy(Message $message): JsonResponse
    {
        $user = Auth::user();
        
        if ($message->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Możesz usuwać tylko swoje wiadomości'
            ], 403);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wiadomość została usunięta'
        ]);
    }

    /**
     * Mark specific message as read
     */
    public function markAsRead(Message $message): JsonResponse
    {
        $user = Auth::user();
        
        if (!$message->conversation->hasParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz dostępu do tej konwersacji'
            ], 403);
        }

        if ($message->user_id !== $user->id && !$message->isRead()) {
            $message->markAsRead();
        }

        return response()->json([
            'success' => true,
            'read_at' => $message->read_at
        ]);
    }

    /**
     * Get unread messages count
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        
        $unreadCount = $user->conversations()
                           ->whereNull('conversation_participants.left_at')
                           ->get()
                           ->sum(function ($conversation) use ($user) {
                               return $conversation->getUnreadCountForUser($user);
                           });

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }
}
