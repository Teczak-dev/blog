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
        'slug' => 'test-post-with-photo',
        'lead' => 'This is a test lead',
        'content' => 'This is test content',
        'photo' => $photo,
    ]);

    $response->assertRedirect('/posts');

    $post = Post::where('slug', 'test-post-with-photo')->first();
    expect($post)->not->toBeNull();
    expect($post->photo)->toStartWith('posts/');

    Storage::disk('public')->assertExists($post->photo);
});

it('can create a post without photo', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->post('/posts', [
        'title' => 'Test Post without Photo',
        'slug' => 'test-post-without-photo',
        'lead' => 'This is a test lead',
        'content' => 'This is test content',
    ]);

    $response->assertRedirect('/posts');

    $post = Post::where('slug', 'test-post-without-photo')->first();
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
        'slug' => 'test-post',
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
    $post = Post::create([
        'title' => 'HTML Test Post',
        'slug' => 'html-test-post',
        'author' => 'Test Author',
        'lead' => 'This is <strong>bold lead</strong> with <em>italic text</em>',
        'content' => 'This is <h2>heading</h2> with <p>paragraph</p> and <a href="#">link</a>',
        'is_published' => true,
    ]);

    $response = $this->get("/posts/{$post->slug}");

    $response->assertSuccessful();
    
    // Debug: print response content
    // dump($response->getContent());
    
    $response->assertSee('<strong>bold lead</strong>', false);
    $response->assertSee('<em>italic text</em>', false);
    $response->assertSee('<h2>heading</h2>', false);
    $response->assertSee('<p>paragraph</p>', false);
    $response->assertSee('<a href="#">link</a>', false);
});

it('displays photo in post show page when available', function () {
    Storage::fake('public');
    $photo = UploadedFile::fake()->image('test.jpg');
    $photoPath = $photo->store('posts', 'public');

    $post = Post::create([
        'title' => 'Photo Test Post',
        'slug' => 'photo-test-post',
        'author' => 'Test Author',
        'content' => 'Test content',
        'photo' => $photoPath,
        'is_published' => true,
    ]);

    $response = $this->get("/posts/{$post->slug}");

    $response->assertSuccessful();
    $response->assertSee("storage/{$photoPath}");
    $response->assertSee('alt="Photo Test Post"', false);
});

it('displays default emoji when no photo in post show page', function () {
    $post = Post::create([
        'title' => 'No Photo Post',
        'slug' => 'no-photo-post',
        'author' => 'Test Author',
        'content' => 'Test content',
        'is_published' => true,
    ]);

    $response = $this->get("/posts/{$post->slug}");

    $response->assertSuccessful();
    $response->assertSee('📝');
});

it('displays post with photo in index page', function () {
    Storage::fake('public');
    $photo = UploadedFile::fake()->image('test.jpg');
    $photoPath = $photo->store('posts', 'public');

    Post::create([
        'title' => 'Index Photo Post',
        'slug' => 'index-photo-post',
        'author' => 'Test Author',
        'content' => 'Test content',
        'photo' => $photoPath,
        'is_published' => true,
    ]);

    $response = $this->get('/posts');

    $response->assertSuccessful();
    $response->assertSee("storage/{$photoPath}");
    $response->assertSee('alt="Index Photo Post"', false);
});
