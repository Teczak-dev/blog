<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Users participating in this conversation
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
                    ->withPivot('joined_at', 'left_at', 'last_read_at')
                    ->withTimestamps();
    }

    /**
     * Messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * Latest message in conversation
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get unread messages count for a specific user
     */
    public function getUnreadCountForUser(User $user): int
    {
        $participant = $this->participants()->where('users.id', $user->id)->first();
        
        if (!$participant) {
            return 0;
        }

        $lastReadAt = $participant->pivot->last_read_at;
        
        if (!$lastReadAt) {
            return $this->messages()->count();
        }

        return $this->messages()
                    ->where('created_at', '>', $lastReadAt)
                    ->where('user_id', '!=', $user->id) // Don't count own messages
                    ->count();
    }

    /**
     * Mark conversation as read for user
     */
    public function markAsReadForUser(User $user): void
    {
        $this->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);
    }

    /**
     * Alias for markAsReadForUser
     */
    public function markAsRead($userId): void
    {
        if ($userId instanceof User) {
            $this->markAsReadForUser($userId);
        } else {
            $user = User::find($userId);
            if ($user) {
                $this->markAsReadForUser($user);
            }
        }
    }

    /**
     * Alias for getUnreadCountForUser
     */
    public function getUnreadMessagesCount($userId): int
    {
        if ($userId instanceof User) {
            return $this->getUnreadCountForUser($userId);
        } else {
            $user = User::find($userId);
            return $user ? $this->getUnreadCountForUser($user) : 0;
        }
    }

    /**
     * Check if user is participant
     */
    public function hasParticipant(User $user): bool
    {
        return $this->participants()
                    ->where('users.id', $user->id)
                    ->whereNull('conversation_participants.left_at')
                    ->exists();
    }

    /**
     * Add participant to conversation
     */
    public function addParticipant(User $user): void
    {
        if (!$this->hasParticipant($user)) {
            $this->participants()->attach($user->id, [
                'joined_at' => now(),
                'last_read_at' => now(),
            ]);
        }
    }

    /**
     * Remove participant from conversation
     */
    public function removeParticipant(User $user): void
    {
        $this->participants()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);
    }

    /**
     * Update last message timestamp
     */
    public function updateLastMessage(): void
    {
        $this->update(['last_message_at' => now()]);
    }

    /**
     * Create a private conversation between two users
     */
    public static function createPrivate(User $user1, User $user2): self
    {
        $conversation = static::create([
            'type' => 'private',
            'last_message_at' => now(),
        ]);

        $conversation->addParticipant($user1);
        $conversation->addParticipant($user2);

        return $conversation;
    }

    /**
     * Find existing private conversation between two users
     */
    public static function findPrivate(User $user1, User $user2): ?self
    {
        return static::where('type', 'private')
                    ->whereHas('participants', function ($query) use ($user1) {
                        $query->where('users.id', $user1->id)
                              ->whereNull('conversation_participants.left_at');
                    })
                    ->whereHas('participants', function ($query) use ($user2) {
                        $query->where('users.id', $user2->id)
                              ->whereNull('conversation_participants.left_at');
                    })
                    ->first();
    }
}
