<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;
    
    // protected $guarded = [];
    protected $fillable = ['title', 'slug', 'lead', 'content', 'author', 'photo', 'is_published'];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
