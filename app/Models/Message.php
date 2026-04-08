<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'type',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * The conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * The user who sent this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if message is read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Get formatted time for display
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('H:i');
    }

    /**
     * Get formatted date for display
     */
    public function getFormattedDateAttribute(): string
    {
        if ($this->created_at->isToday()) {
            return 'Dziś';
        }
        
        if ($this->created_at->isYesterday()) {
            return 'Wczoraj';
        }

        return $this->created_at->format('d.m.Y');
    }

    /**
     * Update conversation's last message timestamp when message is created
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            $message->conversation->updateLastMessage();
        });
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read messages
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }
}
