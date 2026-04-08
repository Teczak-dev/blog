<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'notify_new_posts' => 'boolean', 
            'notify_messages' => 'boolean',
            'notify_friend_requests' => 'boolean',
        ]);

        $request->user()->fill([
            'email_notifications' => $request->boolean('email_notifications'),
            'notify_new_posts' => $request->boolean('notify_new_posts'),
            'notify_messages' => $request->boolean('notify_messages'),
            'notify_friend_requests' => $request->boolean('notify_friend_requests'),
        ]);

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'notification-preferences-updated');
    }

    /**
     * Mute/unmute a user
     */
    public function toggleMute(Request $request, \App\Models\User $user): JsonResponse
    {
        $currentUser = $request->user();
        
        if ($currentUser->hasMuted($user)) {
            $currentUser->unmuteUser($user);
            $message = 'Użytkownik został odciszony';
            $isMuted = false;
        } else {
            $currentUser->muteUser($user);
            $message = 'Użytkownik został wyciszony';
            $isMuted = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_muted' => $isMuted,
        ]);
    }
}
