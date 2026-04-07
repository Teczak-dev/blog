<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('can display edit form for existing post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);

    $response = $this->get("/posts/{$post->id}/edit");

    $response->assertSuccessful();
    $response->assertSee('value="' . $post->title . '"', false);
    $response->assertSee('value="' . $post->category . '"', false);
    $response->assertSee($post->content);
});

it('can update a post without changing photo', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'title' => 'Original Title',
        'content' => 'Original Content',
        'user_id' => $user->id,
    ]);
    
    $this->actingAs($user);

    $response = $this->put("/posts/{$post->id}", [
        'title' => 'Updated Title',
        'category' => 'Updated Category',
        'lead' => 'Updated lead',
        'content' => 'Updated Content',
        'category_color' => 'blue',
        'tags' => 'tag1, tag2',
        'read_time_minutes' => 5,
    ]);

    $response->assertRedirect("/posts/{$post->id}");

    $post->refresh();
    expect($post->title)->toBe('Updated Title');
    expect($post->content)->toBe('Updated Content');
    expect($post->lead)->toBe('Updated lead');
    expect($post->category)->toBe('Updated Category');
});

it('can update a post with new photo', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    
    $oldPhoto = UploadedFile::fake()->image('old.jpg');
    $oldPhotoPath = $oldPhoto->store('posts', 'public');

    $post = Post::factory()->create([
        'photo' => $oldPhotoPath,
        'user_id' => $user->id,
    ]);
    
    $this->actingAs($user);

    $newPhoto = UploadedFile::fake()->image('new.jpg');

    $response = $this->put("/posts/{$post->id}", [
        'title' => $post->title,
        'category' => $post->category,
        'content' => $post->content,
        'photo' => $newPhoto,
        'category_color' => 'blue',
    ]);

    $response->assertRedirect("/posts/{$post->id}");

    $post->refresh();
    expect($post->photo)->toStartWith('posts/');
    expect($post->photo)->not->toBe($oldPhotoPath);
    Storage::disk('public')->assertExists($post->photo);
});

it('validates edit form inputs', function ($field, $value, $expectedError) {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    
    $this->actingAs($user);

    $validData = [
        'title' => 'Valid Title',
        'category' => 'Valid Category',
        'content' => 'Valid Content',
        'category_color' => 'blue',
    ];

    $invalidData = array_merge($validData, [$field => $value]);

    $response = $this->put("/posts/{$post->id}", $invalidData);

    $response->assertSessionHasErrors($field);
})->with([
    'empty title' => ['title', '', 'required'],
    'too long title' => ['title', str_repeat('a', 256), 'max'],
    'empty content' => ['content', '', 'required'],
    'invalid photo' => ['photo', 'not-a-file', 'image'],
]);

it('can update category and color', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'category' => 'Original Category',
        'category_color' => 'blue',
        'user_id' => $user->id,
    ]);
    
    $this->actingAs($user);

    $response = $this->put("/posts/{$post->id}", [
        'title' => $post->title,
        'category' => 'New Category',
        'category_color' => 'red',
        'content' => $post->content,
    ]);

    $response->assertRedirect("/posts/{$post->id}");

    $post->refresh();
    expect($post->category)->toBe('New Category');
    expect($post->category_color)->toBe('red');
});

it('can update tags and reading time', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'tags' => ['old', 'tags'],
        'read_time_minutes' => 5,
        'user_id' => $user->id,
    ]);
    
    $this->actingAs($user);

    $response = $this->put("/posts/{$post->id}", [
        'title' => $post->title,
        'category' => $post->category,
        'content' => $post->content,
        'tags' => 'new, updated, tags',
        'read_time_minutes' => 10,
        'category_color' => 'blue',
    ]);

    $response->assertRedirect("/posts/{$post->id}");

    $post->refresh();
    expect($post->tags)->toBe(['new', 'updated', 'tags']);
    expect($post->read_time_minutes)->toBe(10);
});
