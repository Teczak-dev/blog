<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('can create a post with photo upload', function () {
    Storage::fake('public');
    
    $user = User::factory()->create();
    $this->actingAs($user);

    $photo = UploadedFile::fake()->image('test-post.jpg', 800, 600);

    $response = $this->post('/posts', [
        'title' => 'Test Post with Photo',
        'category' => 'test-category',
        'category_color' => 'blue',
        'lead' => 'This is a test lead',
        'content' => 'This is test content',
        'photo' => $photo,
        'tags' => 'test, photo',
        'read_time_minutes' => 5,
    ]);

    $response->assertRedirect('/posts');

    $post = Post::where('title', 'Test Post with Photo')->first();
    expect($post)->not->toBeNull();
    expect($post->photo)->toStartWith('posts/');

    Storage::disk('public')->assertExists($post->photo);
});

it('can create a post without photo', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->post('/posts', [
        'title' => 'Test Post without Photo',
        'category' => 'test-category',
        'category_color' => 'green', 
        'lead' => 'This is a test lead',
        'content' => 'This is test content',
        'tags' => 'test',
        'read_time_minutes' => 3,
    ]);

    $response->assertRedirect('/posts');

    $post = Post::where('title', 'Test Post without Photo')->first();
    expect($post)->not->toBeNull();
    expect($post->photo)->toBeNull();
});

it('validates photo upload requirements', function ($fileType, $expectedError) {
    Storage::fake('public');
    
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $invalidPhoto = match ($fileType) {
        'large file' => UploadedFile::fake()->image('huge.jpg')->size(3000),
        'non-image file' => UploadedFile::fake()->create('document.pdf'),
        'unsupported format' => UploadedFile::fake()->create('image.bmp'),
    };

    $response = $this->post('/posts', [
        'title' => 'Test Post',
        'category' => 'test-category',
        'category_color' => 'blue',
        'content' => 'This is test content',
        'photo' => $invalidPhoto,
    ]);

    $response->assertSessionHasErrors('photo');
})->with([
    'large file' => ['large file', 'max'],
    'non-image file' => ['non-image file', 'image'],
    'unsupported format' => ['unsupported format', 'mimes'],
]);

it('renders HTML content correctly in post show page', function () {
    $user = User::factory()->create();
    
    $post = Post::create([
        'title' => 'HTML Test Post',
        'category' => 'html-test',
        'category_color' => 'blue',
        'author' => 'Test Author',
        'user_id' => $user->id,
        'lead' => 'This is <strong>bold lead</strong> with <em>italic text</em>',
        'content' => 'This is <h2>heading</h2> with <p>paragraph</p> and <a href="#">link</a>',
        'is_published' => true,
    ]);

    $response = $this->get("/posts/{$post->id}");

    $response->assertSuccessful();
    
    $response->assertSee('<strong>bold lead</strong>', false);
    $response->assertSee('<em>italic text</em>', false);
    $response->assertSee('<h2>heading</h2>', false);
    $response->assertSee('<p>paragraph</p>', false);
    $response->assertSee('<a href="#">link</a>', false);
});

it('displays photo in post show page when available', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('test.jpg');
    $photoPath = $photo->store('posts', 'public');

    $post = Post::create([
        'title' => 'Photo Test Post',
        'category' => 'photo-test',
        'category_color' => 'blue',
        'author' => 'Test Author',
        'user_id' => $user->id,
        'content' => 'Test content',
        'photo' => $photoPath,
        'is_published' => true,
    ]);

    $response = $this->get("/posts/{$post->id}");

    $response->assertSuccessful();
    $response->assertSee("storage/{$photoPath}");
    $response->assertSee('alt="Photo Test Post"', false);
});

it('displays default emoji when no photo in post show page', function () {
    $user = User::factory()->create();
    
    $post = Post::create([
        'title' => 'No Photo Post',
        'category' => 'no-photo',
        'category_color' => 'blue',
        'author' => 'Test Author',
        'user_id' => $user->id,
        'content' => 'Test content',
        'is_published' => true,
    ]);

    $response = $this->get("/posts/{$post->id}");

    $response->assertSuccessful();
    $response->assertSee('📝');
});

it('displays post with photo in index page', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $photo = UploadedFile::fake()->image('test.jpg');
    $photoPath = $photo->store('posts', 'public');

    Post::create([
        'title' => 'Index Photo Post',
        'category' => 'index-photo',
        'category_color' => 'blue',
        'author' => 'Test Author',
        'user_id' => $user->id,
        'content' => 'Test content',
        'photo' => $photoPath,
        'is_published' => true,
    ]);

    $response = $this->get('/posts');

    $response->assertSuccessful();
    $response->assertSee("storage/{$photoPath}");
    $response->assertSee('alt="Index Photo Post"', false);
});
