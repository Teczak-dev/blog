<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

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
        ]);

        return redirect()->route('posts.show', $slug)
            ->with('success', 'Komentarz został dodany pomyślnie!');
    }
}
