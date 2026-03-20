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
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('posts.show', [
            'post' => $post
        ]);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function edit(string $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        return view('posts.update', [
            'post' => $post
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        $parameters = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'photo' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string']
        ]);

        $post->title = $parameters['title'];
        $post->slug = $parameters['slug'];
        $post->photo = $parameters['photo'];
        $post->author = $parameters['author'];
        $post->content = $parameters['content'];

        $post->save();

        return redirect()->route('posts.show', $post->slug);
    }

    public function store(Request $request)
    {
        $parameters = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:posts,slug'],
            'photo' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'content' =>['required', 'string']
        ]);

        $post = new Post();

        $post->title = $parameters['title'];
        $post->slug = $parameters['slug'];  
        $post->photo = $parameters['photo'];
        $post->author = $parameters['author'];
        $post->content = $parameters['content'];

        $post->save();

        return redirect()->route('posts.index');
    }
}
