<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * List all users for discovery
     */
    public function index(Request $request)
    {
        $query = User::where('id', '!=', Auth::id())
                     ->withCount(['posts', 'followers', 'following']);
        
        // Search by name if query provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Filter options
        if ($request->has('verified') && $request->verified === '1') {
            $query->whereNotNull('email_verified_at');
        }
        
        $users = $query->orderBy('posts_count', 'desc')
                      ->orderBy('followers_count', 'desc')
                      ->paginate(12)
                      ->appends($request->query());
        
        return view('users.index', compact('users'));
    }

    /**
     * Show user profile
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Load relationships
        $user->loadCount(['posts', 'followers', 'following']);
        
        // Get user's recent posts
        $posts = $user->posts()
                     ->where('is_published', true)
                     ->orderBy('created_at', 'desc')
                     ->paginate(6);

        // Get relationship status if logged in
        $isFollowing = false;
        $friendshipStatus = null;
        $canSendFriendRequest = false;
        $canAcceptFriendRequest = false;
        
        if ($currentUser && $currentUser->id !== $user->id) {
            $isFollowing = $currentUser->isFollowing($user);
            
            // Check friendship status
            $friendship = \App\Models\Friendship::where(function ($query) use ($currentUser, $user) {
                $query->where('requester_id', $currentUser->id)->where('addressee_id', $user->id);
            })->orWhere(function ($query) use ($currentUser, $user) {
                $query->where('requester_id', $user->id)->where('addressee_id', $currentUser->id);
            })->first();

            if ($friendship) {
                $friendshipStatus = $friendship->status;
                $canAcceptFriendRequest = $friendship->status === 'pending' && $friendship->addressee_id === $currentUser->id;
            } else {
                $canSendFriendRequest = true;
            }
        }

        return view('users.profile', compact(
            'user', 
            'posts', 
            'isFollowing', 
            'friendshipStatus', 
            'canSendFriendRequest', 
            'canAcceptFriendRequest'
        ));
    }

    /**
     * Show user's followers
     */
    public function followers(User $user)
    {
        $followers = $user->followers()
                          ->withPivot('created_at')
                          ->orderBy('follows.created_at', 'desc')
                          ->paginate(20);

        return view('users.followers', compact('user', 'followers'));
    }

    /**
     * Show users that this user is following
     */
    public function following(User $user)
    {
        $following = $user->following()
                          ->withPivot('created_at')
                          ->orderBy('follows.created_at', 'desc')
                          ->paginate(20);

        return view('users.following', compact('user', 'following'));
    }

    /**
     * Show user's friends
     */
    public function friends(User $user)
    {
        // Get accepted friendships in both directions
        $sentFriends = $user->sentFriendRequests()
                           ->where('status', 'accepted')
                           ->with('addressee')
                           ->get()
                           ->pluck('addressee');
                           
        $receivedFriends = $user->receivedFriendRequests()
                               ->where('status', 'accepted')
                               ->with('requester')
                               ->get()
                               ->pluck('requester');
                               
        $friends = $sentFriends->merge($receivedFriends)->unique('id');

        return view('users.friends', compact('user', 'friends'));
    }
}
