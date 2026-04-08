<?php

use App\Events\CommentCreated;
use App\Listeners\SendCommentNotification;
use App\Mail\NewCommentNotification;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('sends notification when comment is created on post', function () {
    Mail::fake();
    
    // Create verified post author with notifications enabled
    $postAuthor = User::factory()->create([
        'email_verified_at' => now(),
        'email_notifications' => true,
    ]);
    
    $post = Post::factory()->create(['user_id' => $postAuthor->id]);
    
    // Create different user who comments
    $commenter = User::factory()->create();
    
    // Create comment
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $commenter->id,
        'is_approved' => true,
    ]);
    
    // Dispatch event manually (in real app this happens in controller)
    CommentCreated::dispatch($comment);
    
    // Assert email was sent
    Mail::assertSent(NewCommentNotification::class, function ($mail) use ($postAuthor) {
        return $mail->hasTo($postAuthor->email);
    });
});

it('does not send notification when post author has notifications disabled', function () {
    Mail::fake();
    
    // Create verified post author with notifications DISABLED
    $postAuthor = User::factory()->create([
        'email_verified_at' => now(),
        'email_notifications' => false,
    ]);
    
    $post = Post::factory()->create(['user_id' => $postAuthor->id]);
    $commenter = User::factory()->create();
    
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $commenter->id,
        'is_approved' => true,
    ]);
    
    CommentCreated::dispatch($comment);
    
    // Assert no email was sent
    Mail::assertNotSent(NewCommentNotification::class);
});

it('does not send notification when post author email is not verified', function () {
    Mail::fake();
    
    // Create unverified post author
    $postAuthor = User::factory()->create([
        'email_verified_at' => null,
        'email_notifications' => true,
    ]);
    
    $post = Post::factory()->create(['user_id' => $postAuthor->id]);
    $commenter = User::factory()->create();
    
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $commenter->id,
        'is_approved' => true,
    ]);
    
    CommentCreated::dispatch($comment);
    
    Mail::assertNotSent(NewCommentNotification::class);
});

it('does not send notification when author comments on their own post', function () {
    Mail::fake();
    
    $postAuthor = User::factory()->create([
        'email_verified_at' => now(),
        'email_notifications' => true,
    ]);
    
    $post = Post::factory()->create(['user_id' => $postAuthor->id]);
    
    // Author comments on their own post
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $postAuthor->id,
        'is_approved' => true,
    ]);
    
    CommentCreated::dispatch($comment);
    
    Mail::assertNotSent(NewCommentNotification::class);
});

it('can update notification preferences in profile', function () {
    $user = User::factory()->create([
        'email_notifications' => true,
    ]);
    
    // Test disabling notifications
    $this->actingAs($user)
        ->patch('/profile/notifications', [
            'email_notifications' => false,
        ])
        ->assertRedirect('/profile')
        ->assertSessionHas('status', 'notifications-updated');
    
    expect($user->fresh()->email_notifications)->toBeFalse();
    
    // Test enabling notifications
    $this->patch('/profile/notifications', [
        'email_notifications' => true,
    ]);
    
    expect($user->fresh()->email_notifications)->toBeTrue();
});

it('notification email contains correct content', function () {
    $postAuthor = User::factory()->create(['name' => 'Jan Kowalski']);
    $post = Post::factory()->create([
        'title' => 'Test Post Title',
        'user_id' => $postAuthor->id,
    ]);
    
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'author_name' => 'Anna Nowak',
        'content' => 'To jest testowy komentarz',
        'is_approved' => true,
    ]);
    
    $mail = new NewCommentNotification($comment, $post, $postAuthor);
    $mailContent = $mail->content();
    
    expect($mailContent->with)->toHaveKey('comment')
        ->and($mailContent->with)->toHaveKey('post')
        ->and($mailContent->with)->toHaveKey('postAuthor')
        ->and($mailContent->with['url'])->toContain('/posts/' . $post->id . '#comment-' . $comment->id);
});

it('creates event when authenticated user posts comment', function () {
    Event::fake();
    
    $postAuthor = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postAuthor->id]);
    $commenter = User::factory()->create();
    
    $this->actingAs($commenter)
        ->post("/posts/{$post->id}/comments", [
            'content' => 'Test comment content',
        ])
        ->assertRedirect("/posts/{$post->id}");
    
    Event::assertDispatched(CommentCreated::class);
});
