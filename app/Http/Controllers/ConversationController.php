<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * Display conversations web interface
     */
    public function showWebInterface()
    {
        $user = Auth::user();
        
        $conversations = $user->conversations()
                             ->with(['participants', 'messages.user'])
                             ->orderByRaw('COALESCE(last_message_at, conversations.created_at) DESC')
                             ->get();
        
        $allUsers = User::where('email_verified_at', '!=', null)->get();
        
        return view('conversations.index', compact('conversations', 'allUsers'));
    }
    
    /**
     * Show specific conversation
     */
    public function show(Conversation $conversation)
    {
        $this->authorize('participate', $conversation);
        
        $user = Auth::user();
        
        $conversations = $user->conversations()
                             ->with(['participants', 'messages.user'])
                             ->orderByRaw('COALESCE(last_message_at, conversations.created_at) DESC')
                             ->get();
        
        $allUsers = User::where('email_verified_at', '!=', null)->get();
        
        $currentConversation = $conversation->load(['participants', 'messages.user']);
        
        // Mark messages as read
        $currentConversation->markAsRead($user->id);
        
        return view('conversations.index', compact('conversations', 'allUsers', 'currentConversation'));
    }
    
    /**
     * Create new conversation (web)
     */
    public function webCreate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        $user = Auth::user();
        $recipientId = $request->input('user_id');
        
        if ($user->id === (int)$recipientId) {
            return back()->withErrors(['user_id' => 'Nie możesz rozpocząć rozmowy sam ze sobą.']);
        }
        
        // Check if conversation already exists
        $existingConversation = $user->conversations()
            ->whereHas('participants', function($query) use ($recipientId) {
                $query->where('user_id', $recipientId);
            })
            ->where('type', 'private')
            ->whereRaw('(SELECT COUNT(*) FROM conversation_participants WHERE conversation_id = conversations.id) = 2')
            ->first();
            
        if ($existingConversation) {
            return redirect()->route('conversations.show', $existingConversation);
        }
        
        // Create new conversation
        $recipient = User::findOrFail($recipientId);
        $conversation = Conversation::createPrivate($user, $recipient);
        
        return redirect()->route('conversations.show', $conversation);
    }

    /**
     * Get all conversations for current user
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        $conversations = $user->conversations()
                             ->with(['latestMessage.user', 'participants'])
                             ->whereNull('conversation_participants.left_at')
                             ->orderBy('last_message_at', 'desc')
                             ->get()
                             ->map(function ($conversation) use ($user) {
                                 $otherParticipants = $conversation->participants
                                                                   ->where('id', '!=', $user->id)
                                                                   ->values();
                                 
                                 return [
                                     'id' => $conversation->id,
                                     'title' => $conversation->title ?: $otherParticipants->pluck('name')->join(', '),
                                     'type' => $conversation->type,
                                     'last_message' => $conversation->latestMessage,
                                     'last_message_at' => $conversation->last_message_at,
                                     'unread_count' => $conversation->getUnreadCountForUser($user),
                                     'participants' => $otherParticipants,
                                 ];
                             });

        return response()->json($conversations);
    }

    /**
     * Create or get private conversation with another user
     */
    public function createPrivate(User $user): JsonResponse
    {
        $currentUser = Auth::user();
        
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Nie możesz rozpocząć konwersacji z samym sobą'
            ], 400);
        }

        // Check if users are friends (required for private messaging)
        if (!$currentUser->isFriend($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Możesz pisać tylko do znajomych'
            ], 403);
        }

        // Try to find existing conversation
        $conversation = Conversation::findPrivate($currentUser, $user);
        
        if (!$conversation) {
            // Create new conversation
            $conversation = Conversation::createPrivate($currentUser, $user);
        }

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'title' => $user->name,
                'type' => $conversation->type,
                'participants' => [$user],
            ]
        ]);
    }

    /**
     * Get conversation details (API)
     */
    public function apiShow(Conversation $conversation): JsonResponse
    {
        $user = Auth::user();
        
        if (!$conversation->hasParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz dostępu do tej konwersacji'
            ], 403);
        }

        $participants = $conversation->participants()
                                   ->whereNull('left_at')
                                   ->get(['users.id', 'users.name', 'users.email']);

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title,
            'type' => $conversation->type,
            'participants' => $participants,
            'unread_count' => $conversation->getUnreadCountForUser($user),
        ]);
    }

    /**
     * Mark conversation as read
     */
    public function markAsRead(Conversation $conversation): JsonResponse
    {
        $user = Auth::user();
        
        if (!$conversation->hasParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz dostępu do tej konwersacji'
            ], 403);
        }

        $conversation->markAsReadForUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Konwersacja została oznaczona jako przeczytana'
        ]);
    }

    /**
     * Leave conversation
     */
    public function leave(Conversation $conversation): JsonResponse
    {
        $user = Auth::user();
        
        if (!$conversation->hasParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie jesteś uczestnikiem tej konwersacji'
            ], 403);
        }

        $conversation->removeParticipant($user);

        return response()->json([
            'success' => true,
            'message' => 'Opuściłeś konwersację'
        ]);
    }
}
