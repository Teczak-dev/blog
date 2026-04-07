<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'category', 'category_color', 'lead', 'content', 'author', 'photo', 'is_published', 'user_id', 'tags', 'read_time_minutes'];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)
                    ->where(function($query) {
                        $query->where('is_approved', true)
                              ->orWhereNotNull('user_id'); // Logged users are auto-approved
                    })
                    ->orderBy('created_at', 'asc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Generate category from title if not provided
    public static function generateCategory($title)
    {
        // Extract main topic/category from title
        $words = explode(' ', $title);
        $category = $words[0]; // Use first word as category
        
        // Common categories mapping
        $categoryMappings = [
            'laravel' => 'Laravel',
            'php' => 'PHP', 
            'react' => 'React',
            'vue' => 'Vue.js',
            'javascript' => 'JavaScript',
            'docker' => 'Docker',
            'tutorial' => 'Tutorial',
            'guide' => 'Guide',
            'tips' => 'Tips',
            'how' => 'Tutorial',
            'what' => 'Guide',
            'introduction' => 'Guide'
        ];
        
        $lowerCategory = strtolower($category);
        return $categoryMappings[$lowerCategory] ?? ucfirst($lowerCategory);
    }

    // Auto-generate read time based on content
    public static function calculateReadTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200)); // Average 200 words per minute
    }

    // Get CSS classes for category color
    public function getCategoryColorClasses()
    {
        $colorMap = [
            'blue' => 'bg-blue-100 text-blue-800',
            'green' => 'bg-green-100 text-green-800',
            'purple' => 'bg-purple-100 text-purple-800',
            'red' => 'bg-red-100 text-red-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
            'pink' => 'bg-pink-100 text-pink-800',
            'gray' => 'bg-gray-100 text-gray-800',
        ];

        return $colorMap[$this->category_color ?? 'blue'] ?? $colorMap['blue'];
    }

    // Get available category colors
    public static function getCategoryColors()
    {
        return [
            'blue' => 'Niebieski',
            'green' => 'Zielony', 
            'purple' => 'Fioletowy',
            'red' => 'Czerwony',
            'yellow' => 'Żółty',
            'indigo' => 'Indygo',
            'pink' => 'Różowy',
            'gray' => 'Szary',
        ];
    }
}
