<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'addressee_id', 
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * The user who sent the friend request
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * The user who received the friend request
     */
    public function addressee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'addressee_id');
    }

    /**
     * Accept the friendship request
     */
    public function accept(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update(['status' => 'accepted']);
        return true;
    }

    /**
     * Block the user (reject and prevent future requests)
     */
    public function block(): bool
    {
        $this->update(['status' => 'blocked']);
        return true;
    }

    /**
     * Check if friendship is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if friendship is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user is blocked
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Prevent self-friendship and duplicate requests
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($friendship) {
            if ($friendship->requester_id === $friendship->addressee_id) {
                throw new \InvalidArgumentException('Users cannot befriend themselves');
            }

            // Check for existing friendship (either direction)
            $existing = static::where(function ($query) use ($friendship) {
                $query->where('requester_id', $friendship->requester_id)
                      ->where('addressee_id', $friendship->addressee_id);
            })->orWhere(function ($query) use ($friendship) {
                $query->where('requester_id', $friendship->addressee_id)
                      ->where('addressee_id', $friendship->requester_id);
            })->first();

            if ($existing) {
                throw new \InvalidArgumentException('Friendship already exists');
            }
        });
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }
}
