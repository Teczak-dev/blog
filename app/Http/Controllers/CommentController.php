<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        // Different validation for logged vs guest users
        if (Auth::check()) {
            $parameters = $request->validate([
                'content' => ['required', 'string', 'max:1000'],
            ]);
            
            Comment::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'content' => $parameters['content'],
                'is_approved' => true, // Logged users are auto-approved
            ]);

            $message = 'Komentarz został dodany pomyślnie!';
        } else {
            $parameters = $request->validate([
                'author_name' => ['required', 'string', 'max:255'],
                'author_email' => ['required', 'email', 'max:255'],
                'content' => ['required', 'string', 'max:1000'],
            ]);

            Comment::create([
                'post_id' => $post->id,
                'author_name' => $parameters['author_name'],
                'author_email' => $parameters['author_email'],
                'content' => $parameters['content'],
                'is_approved' => false, // Guests need approval
            ]);

            $message = 'Komentarz został wysłany i oczekuje na moderację. Zostanie opublikowany po zatwierdzeniu przez administratora.';
        }

        return redirect()->route('posts.show', $id)
            ->with('success', $message);
    }

    public function loadMore(Request $request, string $postId)
    {
        $offset = $request->get('offset', 0);
        $limit = 5; // Load 5 more comments at a time
        
        $post = Post::findOrFail($postId);
        
        $comments = $post->approvedComments()
            ->skip($offset)
            ->take($limit)
            ->get();
            
        $hasMore = $post->approvedComments()->count() > ($offset + $limit);
        
        return response()->json([
            'comments' => $comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'author_name' => $comment->author_display_name,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->format('d.m.Y H:i'),
                    'is_from_logged_user' => $comment->isFromLoggedUser(),
                ];
            }),
            'hasMore' => $hasMore,
        ]);
    }
}
