<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    /**
     * Show friends management interface
     */
    public function showWebInterface()
    {
        $user = Auth::user();
        
        // Get friends
        $friends = $user->getFriends();
        
        // Get pending friend requests (received)
        $pendingRequests = Friendship::where('addressee_id', $user->id)
                                   ->where('status', 'pending')
                                   ->with('requester')
                                   ->get();
        
        // Get sent friend requests
        $sentRequests = Friendship::where('requester_id', $user->id)
                                 ->where('status', 'pending')
                                 ->with('addressee')
                                 ->get();
        
        return view('friends.index', compact('friends', 'pendingRequests', 'sentRequests'));
    }
    
    /**
     * Reject a friend request
     */
    public function reject(Friendship $friendship): JsonResponse
    {
        $user = Auth::user();
        
        if ($friendship->addressee_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Nie możesz odrzucić tego zaproszenia'
            ], 403);
        }
        
        $friendship->update(['status' => 'blocked']);
        
        return response()->json([
            'success' => true,
            'message' => 'Zaproszenie zostało odrzucone'
        ]);
    }
    
    /**
     * Cancel a sent friend request
     */
    public function cancel(Friendship $friendship): JsonResponse
    {
        $user = Auth::user();
        
        if ($friendship->requester_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Nie możesz anulować tego zaproszenia'
            ], 403);
        }
        
        if ($friendship->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'To zaproszenie nie może być anulowane'
            ], 400);
        }
        
        $friendship->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Zaproszenie zostało anulowane'
        ]);
    }

    /**
     * Send a friend request
     */
    public function sendRequest(User $user): JsonResponse
    {
        $currentUser = Auth::user();
        
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Nie możesz wysłać zaproszenia do siebie'
            ], 400);
        }

        try {
            Friendship::create([
                'requester_id' => $currentUser->id,
                'addressee_id' => $user->id,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Zaproszenie zostało wysłane',
                'status' => 'pending'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Zaproszenie już istnieje lub wystąpił błąd'
            ], 400);
        }
    }

    /**
     * Accept a friend request
     */
    public function accept(Friendship $friendship): JsonResponse
    {
        $currentUser = Auth::user();
        
        if ($friendship->addressee_id !== $currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz uprawnień do akceptowania tego zaproszenia'
            ], 403);
        }

        if (!$friendship->accept()) {
            return response()->json([
                'success' => false,
                'message' => 'Nie można zaakceptować tego zaproszenia'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Zaproszenie zostało zaakceptowane',
            'status' => 'accepted'
        ]);
    }

    /**
     * Remove friendship or cancel friend request
     */
    public function remove(User $user): JsonResponse
    {
        $currentUser = Auth::user();
        
        $friendship = Friendship::where(function ($query) use ($currentUser, $user) {
            $query->where('requester_id', $currentUser->id)->where('addressee_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('requester_id', $user->id)->where('addressee_id', $currentUser->id);
        })->first();

        if (!$friendship) {
            return response()->json([
                'success' => false,
                'message' => 'Nie znaleziono znajomości'
            ], 404);
        }

        $friendship->delete();

        return response()->json([
            'success' => true,
            'message' => 'Znajomość została usunięta'
        ]);
    }

    /**
     * Get pending friend requests for current user
     */
    public function pendingRequests(): JsonResponse
    {
        $currentUser = Auth::user();
        
        $requests = $currentUser->receivedFriendRequests()
                                ->where('status', 'pending')
                                ->with('requester')
                                ->orderBy('created_at', 'desc')
                                ->get();

        return response()->json($requests);
    }
}
