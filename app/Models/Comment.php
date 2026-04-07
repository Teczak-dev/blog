<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'author_name', 
        'author_email',
        'content',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope for approved comments
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    // Scope for pending comments (guests only)
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_approved', false)
                    ->whereNull('user_id');
    }

    // Check if comment is from logged user (auto-approved)
    public function isFromLoggedUser(): bool
    {
        return !is_null($this->user_id);
    }

    // Get display name for comment author
    public function getAuthorDisplayNameAttribute(): string
    {
        return $this->user ? $this->user->name : $this->author_name;
    }

    // Get author email for display
    public function getAuthorDisplayEmailAttribute(): string
    {
        return $this->user ? $this->user->email : $this->author_email;
    }
}
