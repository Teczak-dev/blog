<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Follow or unfollow a user
     */
    public function toggle(User $user): JsonResponse
    {
        $currentUser = Auth::user();
        
        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Nie możesz obserwować siebie'
            ], 400);
        }

        $existingFollow = Follow::where('follower_id', $currentUser->id)
                               ->where('followed_id', $user->id)
                               ->first();

        if ($existingFollow) {
            // Unfollow
            $existingFollow->delete();
            $isFollowing = false;
            $message = 'Przestałeś obserwować tego użytkownika';
        } else {
            // Follow
            Follow::create([
                'follower_id' => $currentUser->id,
                'followed_id' => $user->id,
            ]);
            $isFollowing = true;
            $message = 'Rozpocząłeś obserwowanie tego użytkownika';
        }

        return response()->json([
            'success' => true,
            'is_following' => $isFollowing,
            'followers_count' => $user->followers()->count(),
            'message' => $message
        ]);
    }

    /**
     * Get list of users that the current user is following
     */
    public function following(Request $request)
    {
        $user = $request->user ?? Auth::user();
        
        $following = $user->following()
                          ->withPivot('created_at')
                          ->orderBy('follows.created_at', 'desc')
                          ->paginate(20);

        if ($request->ajax()) {
            return response()->json($following);
        }

        return view('users.following', compact('user', 'following'));
    }

    /**
     * Get list of users following the current user
     */
    public function followers(Request $request)
    {
        $user = $request->user ?? Auth::user();
        
        $followers = $user->followers()
                          ->withPivot('created_at')
                          ->orderBy('follows.created_at', 'desc')
                          ->paginate(20);

        if ($request->ajax()) {
            return response()->json($followers);
        }

        return view('users.followers', compact('user', 'followers'));
    }

    /**
     * Check if current user is following another user
     */
    public function status(User $user): JsonResponse
    {
        $currentUser = Auth::user();
        
        $isFollowing = $currentUser ? $currentUser->isFollowing($user) : false;
        
        return response()->json([
            'is_following' => $isFollowing,
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
        ]);
    }

    /**
     * Get suggested users to follow
     */
    public function suggestions(): JsonResponse
    {
        $currentUser = Auth::user();
        
        // Get users not already followed, excluding current user
        $suggestions = User::whereNotIn('id', 
                            $currentUser->following()->pluck('follows.followed_id')->concat([$currentUser->id])
                          )
                          ->has('posts') // Only users with posts
                          ->withCount('followers')
                          ->orderBy('followers_count', 'desc')
                          ->limit(5)
                          ->get();

        return response()->json($suggestions);
    }
}
