<?php

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('can display edit form for existing post', function () {
    $post = Post::factory()->create();

    $response = $this->get("/posts/{$post->slug}/edit");

    $response->assertSuccessful();
    $response->assertSee('value="' . $post->title . '"', false);
    $response->assertSee('value="' . $post->slug . '"', false);
    $response->assertSee($post->content);
});

it('can update a post without changing photo', function () {
    $post = Post::factory()->create([
        'title' => 'Original Title',
        'content' => 'Original Content'
    ]);

    $response = $this->put("/posts/{$post->slug}", [
        'title' => 'Updated Title',
        'slug' => $post->slug,
        'author' => $post->author,
        'lead' => 'Updated lead',
        'content' => 'Updated Content',
    ]);

    $response->assertRedirect("/posts/{$post->slug}");

    $post->refresh();
    expect($post->title)->toBe('Updated Title');
    expect($post->content)->toBe('Updated Content');
    expect($post->lead)->toBe('Updated lead');
});

it('can update a post with new photo', function () {
    Storage::fake('public');
    $oldPhoto = UploadedFile::fake()->image('old.jpg');
    $oldPhotoPath = $oldPhoto->store('posts', 'public');

    $post = Post::factory()->create(['photo' => $oldPhotoPath]);

    $newPhoto = UploadedFile::fake()->image('new.jpg');

    $response = $this->put("/posts/{$post->slug}", [
        'title' => $post->title,
        'slug' => $post->slug,
        'author' => $post->author,
        'content' => $post->content,
        'photo' => $newPhoto,
    ]);

    $response->assertRedirect("/posts/{$post->slug}");

    $post->refresh();
    expect($post->photo)->toStartWith('posts/');
    expect($post->photo)->not->toBe($oldPhotoPath);
    Storage::disk('public')->assertExists($post->photo);
});

it('validates edit form inputs', function ($field, $value, $expectedError) {
    $post = Post::factory()->create();

    $validData = [
        'title' => 'Valid Title',
        'slug' => $post->slug,
        'author' => 'Valid Author',
        'content' => 'Valid Content',
    ];

    $invalidData = array_merge($validData, [$field => $value]);

    $response = $this->put("/posts/{$post->slug}", $invalidData);

    $response->assertSessionHasErrors($field);
})->with([
    'empty title' => ['title', '', 'required'],
    'too long title' => ['title', str_repeat('a', 256), 'max'],
    'empty author' => ['author', '', 'required'],
    'empty content' => ['content', '', 'required'],
    'invalid photo' => ['photo', 'not-a-file', 'image'],
]);

it('can update slug to new unique value', function () {
    $post = Post::factory()->create(['slug' => 'original-slug']);

    $response = $this->put("/posts/{$post->slug}", [
        'title' => $post->title,
        'slug' => 'new-unique-slug',
        'author' => $post->author,
        'content' => $post->content,
    ]);

    $response->assertRedirect('/posts/new-unique-slug');

    $post->refresh();
    expect($post->slug)->toBe('new-unique-slug');
});

it('prevents updating to duplicate slug', function () {
    $existingPost = Post::factory()->create(['slug' => 'existing-slug']);
    $post = Post::factory()->create(['slug' => 'original-slug']);

    $response = $this->put("/posts/{$post->slug}", [
        'title' => $post->title,
        'slug' => 'existing-slug',
        'author' => $post->author,
        'content' => $post->content,
    ]);

    $response->assertSessionHasErrors('slug');
});
