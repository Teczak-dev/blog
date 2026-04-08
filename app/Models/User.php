<?php

namespace App\Models;

use App\Mail\VerifyEmailMail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_notifications',
        'notify_new_posts',
        'notify_messages', 
        'notify_friend_requests',
        'muted_users',
        'theme_preference',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_notifications' => 'boolean',
            'notify_new_posts' => 'boolean',
            'notify_messages' => 'boolean',
            'notify_friend_requests' => 'boolean',
            'muted_users' => 'array',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // === FOLLOW RELATIONSHIPS ===
    
    /**
     * Users that this user is following
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')
                    ->withTimestamps();
    }

    /**
     * Users that follow this user
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
                    ->withTimestamps();
    }

    // === FRIENDSHIP RELATIONSHIPS ===
    
    /**
     * Friendship requests sent by this user
     */
    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'requester_id');
    }

    /**
     * Friendship requests received by this user
     */
    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'addressee_id');
    }

    /**
     * All friends (accepted friendships)
     */
    public function friends()
    {
        $sent = $this->belongsToMany(User::class, 'friendships', 'requester_id', 'addressee_id')
                     ->wherePivot('status', 'accepted')
                     ->withTimestamps();
                     
        $received = $this->belongsToMany(User::class, 'friendships', 'addressee_id', 'requester_id')
                         ->wherePivot('status', 'accepted')
                         ->withTimestamps();
                         
        return $sent->union($received);
    }

    // === CONVERSATION RELATIONSHIPS ===
    
    /**
     * Conversations this user participates in
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
                    ->withPivot('joined_at', 'left_at', 'last_read_at')
                    ->withTimestamps();
    }

    /**
     * Messages sent by this user
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Wszyscy zarejestrowani użytkownicy mogą uzyskać dostęp do panelu
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->id, 'hash' => sha1($this->email)]
        );

        Mail::to($this->email)->send(
            new VerifyEmailMail($verificationUrl, $this->name)
        );
    }

    // === HELPER METHODS ===

    /**
     * Check if this user is following another user
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()->where('followed_id', $user->id)->exists();
    }

    /**
     * Check if this user is friends with another user
     */
    public function isFriend(User $user): bool
    {
        return $this->sentFriendRequests()
                    ->where('addressee_id', $user->id)
                    ->where('status', 'accepted')
                    ->exists()
               || $this->receivedFriendRequests()
                    ->where('requester_id', $user->id)
                    ->where('status', 'accepted')
                    ->exists();
    }

    /**
     * Check if there's a pending friendship request to this user
     */
    public function hasPendingFriendRequestFrom(User $user): bool
    {
        return $this->receivedFriendRequests()
                    ->where('requester_id', $user->id)
                    ->where('status', 'pending')
                    ->exists();
    }

    /**
     * Check if this user has sent a pending friend request to another user
     */
    public function hasSentFriendRequestTo(User $user): bool
    {
        return $this->sentFriendRequests()
                    ->where('addressee_id', $user->id)
                    ->where('status', 'pending')
                    ->exists();
    }

    /**
     * Get all friends (accepted friendships in both directions)
     */
    public function getFriends()
    {
        // Get accepted friendships where this user sent the request
        $sentFriends = $this->sentFriendRequests()
                           ->where('status', 'accepted')
                           ->with('addressee')
                           ->get()
                           ->pluck('addressee');
                           
        // Get accepted friendships where this user received the request
        $receivedFriends = $this->receivedFriendRequests()
                               ->where('status', 'accepted')
                               ->with('requester')
                               ->get()
                               ->pluck('requester');
                               
        return $sentFriends->merge($receivedFriends)->unique('id');
    }

    /**
     * Check if user is muted by this user
     */
    public function hasMuted(User $user): bool
    {
        $mutedUsers = $this->muted_users ?? [];
        return in_array($user->id, $mutedUsers);
    }

    /**
     * Mute a user
     */
    public function muteUser(User $user): void
    {
        $mutedUsers = $this->muted_users ?? [];
        if (!in_array($user->id, $mutedUsers)) {
            $mutedUsers[] = $user->id;
            $this->update(['muted_users' => $mutedUsers]);
        }
    }

    /**
     * Unmute a user
     */
    public function unmuteUser(User $user): void
    {
        $mutedUsers = $this->muted_users ?? [];
        $mutedUsers = array_values(array_filter($mutedUsers, fn($id) => $id !== $user->id));
        $this->update(['muted_users' => $mutedUsers]);
    }

    /**
     * Get unread messages count
     */
    public function getUnreadMessagesCount(): int
    {
        return $this->conversations()
                   ->join('messages', 'conversations.id', '=', 'messages.conversation_id')
                   ->whereNull('messages.deleted_at')
                   ->where('messages.user_id', '!=', $this->id)
                   ->where(function($query) {
                       $query->whereNull('conversation_participants.last_read_at')
                             ->orWhere('messages.created_at', '>', 'conversation_participants.last_read_at');
                   })
                   ->count();
    }

    /**
     * Get pending friend requests count (received by this user)
     */
    public function getPendingFriendRequestsCount(): int
    {
        return $this->receivedFriendRequests()
                   ->where('status', 'pending')
                   ->count();
    }

    /**
     * Check if user has any notifications
     */
    public function hasNotifications(): bool
    {
        return $this->getUnreadMessagesCount() > 0 || $this->getPendingFriendRequestsCount() > 0;
    }
}
