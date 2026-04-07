<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return view('posts.index', [
            'posts' => $posts
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->with('comments')->firstOrFail();

        return view('posts.show', [
            'post' => $post,
        ]);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function edit(string $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        
        // Check if user can edit this post
        if (auth()->user() && $post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('posts.edit', [
            'post' => $post
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:5|max:255',
            'slug' => 'required|unique:posts|min:5|max:255',
            'lead' => 'nullable|max:500',
            'content' => 'required|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('posts', 'public');
        }

        Post::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'lead' => $request->lead,
            'content' => $request->content,
            'photo' => $photoPath,
            'author' => auth()->user()->name,
            'user_id' => auth()->id(),
            'is_published' => true,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post został utworzony!');
    }

    public function update(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        
        // Check if user can edit this post
        if (auth()->user() && $post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|min:5|max:255',
            'slug' => 'required|min:5|max:255|unique:posts,slug,' . $post->id,
            'lead' => 'nullable|max:500',
            'content' => 'required|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = $post->photo; // Keep existing photo by default
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($post->photo) {
                \Storage::disk('public')->delete($post->photo);
            }
            $photoPath = $request->file('photo')->store('posts', 'public');
        }

        $post->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'lead' => $request->lead,
            'content' => $request->content,
            'photo' => $photoPath,
        ]);

        return redirect()->route('posts.show', $post->slug)->with('success', 'Post został zaktualizowany!');
    }
}
