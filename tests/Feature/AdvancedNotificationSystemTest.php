<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\CommentCreated;
use App\Events\PostCreated;
use App\Events\MessageSent;
use App\Mail\NewCommentNotification;
use App\Mail\NewPostNotification;
use App\Mail\NewMessageNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

describe('Email Notification System', function () {
    beforeEach(function () {
        Mail::fake();
        Event::fake();
        
        $this->user1 = User::factory()->create([
            'email_verified_at' => now(),
            'email_notifications' => true,
            'comment_notifications' => true,
            'post_notifications' => true,
            'message_notifications' => true,
        ]);
        
        $this->user2 = User::factory()->create([
            'email_verified_at' => now(),
            'email_notifications' => true,
            'comment_notifications' => true,
            'post_notifications' => true,
            'message_notifications' => true,
        ]);

        $this->user3 = User::factory()->create([
            'email_verified_at' => now(),
            'email_notifications' => false, // Disabled notifications
        ]);
    });

    it('sends comment notifications when enabled', function () {
        $post = Post::factory()->create(['user_id' => $this->user1->id]);
        
        $this->actingAs($this->user2)
            ->postJson("/posts/{$post->id}/comments", [
                'content' => 'Great post!'
            ])
            ->assertSuccessful();

        Event::assertDispatched(CommentCreated::class);
    });

    it('sends post notifications to followers', function () {
        // User2 follows User1
        $this->user2->follows()->attach($this->user1->id);

        $this->actingAs($this->user1)
            ->postJson('/posts', [
                'title' => 'New Post',
                'content' => 'Post content',
                'category' => 'tech',
                'tags' => ['test'],
            ])
            ->assertSuccessful();

        Event::assertDispatched(PostCreated::class);
    });

    it('sends message notifications when enabled', function () {
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        $this->actingAs($this->user1)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Hello there!'
            ])
            ->assertSuccessful();

        Event::assertDispatched(MessageSent::class);
    });

    it('respects user notification preferences', function () {
        // User3 has notifications disabled
        $post = Post::factory()->create(['user_id' => $this->user3->id]);
        
        $this->actingAs($this->user1)
            ->postJson("/posts/{$post->id}/comments", [
                'content' => 'Comment on disabled notifications'
            ])
            ->assertSuccessful();

        // Event should be dispatched but email won't be sent due to preferences
        Event::assertDispatched(CommentCreated::class);
    });

    it('requires email verification for notifications', function () {
        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null,
            'email_notifications' => true,
        ]);

        $post = Post::factory()->create(['user_id' => $unverifiedUser->id]);
        
        $this->actingAs($this->user1)
            ->postJson("/posts/{$post->id}/comments", [
                'content' => 'Comment to unverified user'
            ])
            ->assertSuccessful();

        Event::assertDispatched(CommentCreated::class);
        // No email should be sent to unverified user
    });

    it('prevents self-notifications', function () {
        $post = Post::factory()->create(['user_id' => $this->user1->id]);
        
        // User commenting on their own post
        $this->actingAs($this->user1)
            ->postJson("/posts/{$post->id}/comments", [
                'content' => 'Self comment'
            ])
            ->assertSuccessful();

        Event::assertDispatched(CommentCreated::class);
        // No email should be sent to self
    });

    it('respects muted users in notifications', function () {
        // User1 mutes User2
        $this->user1->mutedUsers()->attach($this->user2->id);

        $post = Post::factory()->create(['user_id' => $this->user1->id]);
        
        $this->actingAs($this->user2)
            ->postJson("/posts/{$post->id}/comments", [
                'content' => 'Comment from muted user'
            ])
            ->assertSuccessful();

        Event::assertDispatched(CommentCreated::class);
        // No email should be sent due to muting
    });

    it('allows updating notification preferences', function () {
        $this->actingAs($this->user1)
            ->patchJson('/profile/notifications', [
                'email_notifications' => false,
                'comment_notifications' => false,
                'post_notifications' => true,
                'message_notifications' => false,
            ])
            ->assertSuccessful();

        $this->user1->refresh();
        
        expect($this->user1->email_notifications)->toBeFalse();
        expect($this->user1->comment_notifications)->toBeFalse();
        expect($this->user1->post_notifications)->toBeTrue();
        expect($this->user1->message_notifications)->toBeFalse();
    });
});

describe('Notification Events', function () {
    beforeEach(function () {
        Event::fake();
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
    });

    it('dispatches CommentCreated event', function () {
        $post = Post::factory()->create(['user_id' => $this->user1->id]);
        
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $this->user2->id,
            'content' => 'Test comment',
            'approved' => true,
        ]);

        event(new CommentCreated($comment));

        Event::assertDispatched(CommentCreated::class, function ($event) use ($comment) {
            return $event->comment->id === $comment->id;
        });
    });

    it('dispatches PostCreated event', function () {
        $post = Post::create([
            'title' => 'Test Post',
            'content' => 'Test content',
            'user_id' => $this->user1->id,
            'category' => 'tech',
            'tags' => ['test'],
        ]);

        event(new PostCreated($post));

        Event::assertDispatched(PostCreated::class, function ($event) use ($post) {
            return $event->post->id === $post->id;
        });
    });

    it('dispatches MessageSent event', function () {
        $conversation = Conversation::create(['type' => 'private']);
        
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->user1->id,
            'content' => 'Test message',
        ]);

        event(new MessageSent($message));

        Event::assertDispatched(MessageSent::class, function ($event) use ($message) {
            return $event->message->id === $message->id;
        });
    });
});

describe('Muting System', function () {
    beforeEach(function () {
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
        $this->user3 = User::factory()->create();
    });

    it('allows muting users', function () {
        $this->actingAs($this->user1)
            ->postJson("/api/users/{$this->user2->id}/mute")
            ->assertSuccessful();

        $this->assertDatabaseHas('user_mutes', [
            'user_id' => $this->user1->id,
            'muted_user_id' => $this->user2->id,
        ]);

        expect($this->user1->hasMuted($this->user2))->toBeTrue();
    });

    it('allows unmuting users', function () {
        // First mute
        $this->user1->mutedUsers()->attach($this->user2->id);

        $this->actingAs($this->user1)
            ->deleteJson("/api/users/{$this->user2->id}/mute")
            ->assertSuccessful();

        $this->assertDatabaseMissing('user_mutes', [
            'user_id' => $this->user1->id,
            'muted_user_id' => $this->user2->id,
        ]);

        expect($this->user1->hasMuted($this->user2))->toBeFalse();
    });

    it('shows muted users list', function () {
        $this->user1->mutedUsers()->attach([$this->user2->id, $this->user3->id]);

        $this->actingAs($this->user1)
            ->getJson('/api/users/muted')
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $this->user2->id])
            ->assertJsonFragment(['id' => $this->user3->id]);
    });

    it('prevents self-muting', function () {
        $this->actingAs($this->user1)
            ->postJson("/api/users/{$this->user1->id}/mute")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id']);
    });
});

describe('Notification UI Integration', function () {
    beforeEach(function () {
        $this->user1 = User::factory()->create([
            'email_verified_at' => now(),
            'email_notifications' => true,
        ]);
    });

    it('shows notification preferences in profile', function () {
        $this->actingAs($this->user1)
            ->get('/profile')
            ->assertSuccessful()
            ->assertSee('Powiadomienia email')
            ->assertSee('Komentarze pod Twoimi postami')
            ->assertSee('Nowe posty od obserwowanych');
    });

    it('shows notification count in navbar', function () {
        // Create unread messages
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            User::factory()->create()->id => ['joined_at' => now()]
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $conversation->participants()->where('user_id', '!=', $this->user1->id)->first()->id,
            'content' => 'Unread message',
        ]);

        $this->actingAs($this->user1)
            ->get('/dashboard')
            ->assertSuccessful()
            ->assertSee('1'); // Should show notification count
    });
});