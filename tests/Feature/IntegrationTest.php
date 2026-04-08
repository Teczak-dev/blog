<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Friendship;
use App\Events\CommentCreated;
use App\Events\PostCreated;
use App\Events\MessageSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

describe('End-to-End Social Media Integration', function () {
    beforeEach(function () {
        Mail::fake();
        Event::fake();
        
        // Create verified users with notifications enabled
        $this->author = User::factory()->create([
            'name' => 'Content Author',
            'email' => 'author@example.com',
            'email_verified_at' => now(),
            'email_notifications' => true,
            'comment_notifications' => true,
            'post_notifications' => true,
            'message_notifications' => true,
        ]);
        
        $this->follower = User::factory()->create([
            'name' => 'Active Follower',
            'email' => 'follower@example.com',
            'email_verified_at' => now(),
            'email_notifications' => true,
            'comment_notifications' => true,
            'post_notifications' => true,
            'message_notifications' => true,
        ]);
        
        $this->friend = User::factory()->create([
            'name' => 'Close Friend',
            'email' => 'friend@example.com',
            'email_verified_at' => now(),
            'email_notifications' => true,
            'comment_notifications' => true,
            'post_notifications' => true,
            'message_notifications' => true,
        ]);
    });

    it('completes full social interaction workflow', function () {
        // Step 1: Follower starts following Author
        $this->actingAs($this->follower)
            ->postJson("/api/follow/{$this->author->id}")
            ->assertSuccessful()
            ->assertJson(['status' => 'following']);

        expect($this->follower->isFollowing($this->author))->toBeTrue();

        // Step 2: Friend sends friendship request to Author
        $this->actingAs($this->friend)
            ->postJson("/api/friends/request/{$this->author->id}")
            ->assertSuccessful()
            ->assertJson(['status' => 'request_sent']);

        $this->assertDatabaseHas('friendships', [
            'requester_id' => $this->friend->id,
            'addressee_id' => $this->author->id,
            'status' => 'pending',
        ]);

        // Step 3: Author accepts friendship request
        $this->actingAs($this->author)
            ->postJson("/api/friends/accept/{$this->friend->id}")
            ->assertSuccessful()
            ->assertJson(['status' => 'accepted']);

        $this->assertDatabaseHas('friendships', [
            'requester_id' => $this->friend->id,
            'addressee_id' => $this->author->id,
            'status' => 'accepted',
        ]);

        // Step 4: Author creates a new post
        $this->actingAs($this->author)
            ->postJson('/posts', [
                'title' => 'My Amazing Journey',
                'content' => 'This is a story about an amazing adventure...',
                'category' => 'travel',
                'tags' => ['adventure', 'travel', 'story'],
            ])
            ->assertSuccessful();

        $post = Post::where('title', 'My Amazing Journey')->first();
        expect($post)->not->toBeNull();
        expect($post->user_id)->toBe($this->author->id);

        // Verify PostCreated event was dispatched
        Event::assertDispatched(PostCreated::class);

        // Step 5: Follower sees and comments on the post
        $this->actingAs($this->follower)
            ->postJson("/posts/{$post->id}/comments", [
                'content' => 'Amazing story! Thanks for sharing.'
            ])
            ->assertSuccessful();

        $comment = Comment::where('content', 'Amazing story! Thanks for sharing.')->first();
        expect($comment)->not->toBeNull();
        expect($comment->user_id)->toBe($this->follower->id);

        // Verify CommentCreated event was dispatched
        Event::assertDispatched(CommentCreated::class);

        // Step 6: Friend initiates private conversation with Author
        $this->actingAs($this->friend)
            ->postJson('/api/conversations/private', [
                'user_id' => $this->author->id
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'type']);

        $conversation = Conversation::where('type', 'private')->first();
        expect($conversation)->not->toBeNull();
        expect($conversation->participants->pluck('id'))->toContain($this->author->id);
        expect($conversation->participants->pluck('id'))->toContain($this->friend->id);

        // Step 7: Friend sends a message in the conversation
        $this->actingAs($this->friend)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Hey! Loved your latest post. We should catch up soon!'
            ])
            ->assertSuccessful();

        $message = Message::where('content', 'Hey! Loved your latest post. We should catch up soon!')->first();
        expect($message)->not->toBeNull();
        expect($message->sender_id)->toBe($this->friend->id);

        // Verify MessageSent event was dispatched
        Event::assertDispatched(MessageSent::class);

        // Step 8: Author responds to the message
        $this->actingAs($this->author)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Absolutely! Let\'s plan something for next week.'
            ])
            ->assertSuccessful();

        // Step 9: Verify notification counts are correct
        expect($this->author->getUnreadMessagesCount())->toBe(0); // Author read their conversation
        expect($this->friend->getUnreadMessagesCount())->toBe(1); // Friend has unread message from author
        expect($this->follower->getUnreadMessagesCount())->toBe(0); // Follower not in conversation

        // Step 10: Author marks conversation as read
        $this->actingAs($this->author)
            ->patchJson("/api/conversations/{$conversation->id}/read")
            ->assertSuccessful();

        // Step 11: Friend reads the conversation
        $this->actingAs($this->friend)
            ->patchJson("/api/conversations/{$conversation->id}/read")
            ->assertSuccessful();

        expect($this->friend->getUnreadMessagesCount())->toBe(0);
    });

    it('handles multiple users interacting with same post', function () {
        $user1 = User::factory()->create(['email_verified_at' => now()]);
        $user2 = User::factory()->create(['email_verified_at' => now()]);
        $user3 = User::factory()->create(['email_verified_at' => now()]);

        // All users follow author
        $user1->follows()->attach($this->author->id);
        $user2->follows()->attach($this->author->id);
        $user3->follows()->attach($this->author->id);

        // Author creates post
        $post = Post::factory()->create([
            'user_id' => $this->author->id,
            'title' => 'Popular Post'
        ]);

        // Multiple users comment
        $this->actingAs($user1)
            ->postJson("/posts/{$post->id}/comments", ['content' => 'First comment!'])
            ->assertSuccessful();

        $this->actingAs($user2)
            ->postJson("/posts/{$post->id}/comments", ['content' => 'Great post!'])
            ->assertSuccessful();

        $this->actingAs($user3)
            ->postJson("/posts/{$post->id}/comments", ['content' => 'Totally agree!'])
            ->assertSuccessful();

        // Verify all comments were created
        expect($post->comments()->count())->toBe(3);
        expect(Comment::where('post_id', $post->id)->count())->toBe(3);

        // Verify events were dispatched for each comment
        Event::assertDispatchedTimes(CommentCreated::class, 3);
    });

    it('tests friend request workflow with multiple users', function () {
        $users = User::factory()->count(3)->create(['email_verified_at' => now()]);

        // User1 sends requests to everyone
        foreach ($users as $user) {
            $this->actingAs($this->author)
                ->postJson("/api/friends/request/{$user->id}")
                ->assertSuccessful();
        }

        // Check all requests are pending
        expect($this->author->sentFriendRequests()->where('status', 'pending')->count())->toBe(3);

        // First user accepts
        $this->actingAs($users[0])
            ->postJson("/api/friends/accept/{$this->author->id}")
            ->assertSuccessful();

        // Second user rejects
        $this->actingAs($users[1])
            ->postJson("/api/friends/reject/{$this->author->id}")
            ->assertSuccessful();

        // Third user blocks
        $this->actingAs($users[2])
            ->postJson("/api/friends/block/{$this->author->id}")
            ->assertSuccessful();

        // Verify final states
        expect(Friendship::where('requester_id', $this->author->id)
            ->where('addressee_id', $users[0]->id)
            ->where('status', 'accepted')->exists())->toBeTrue();

        expect(Friendship::where('requester_id', $this->author->id)
            ->where('addressee_id', $users[1]->id)
            ->where('status', 'rejected')->exists())->toBeTrue();

        expect(Friendship::where('requester_id', $this->author->id)
            ->where('addressee_id', $users[2]->id)
            ->where('status', 'blocked')->exists())->toBeTrue();
    });

    it('tests group conversation dynamics', function () {
        $participant1 = User::factory()->create(['email_verified_at' => now()]);
        $participant2 = User::factory()->create(['email_verified_at' => now()]);

        // Create group conversation
        $conversation = Conversation::create(['type' => 'group']);
        $conversation->participants()->attach([
            $this->author->id => ['joined_at' => now()],
            $participant1->id => ['joined_at' => now()],
            $participant2->id => ['joined_at' => now()],
        ]);

        // Each participant sends a message
        $messages = [
            [$this->author, 'Welcome to our group chat!'],
            [$participant1, 'Thanks for creating this!'],
            [$participant2, 'Looking forward to our discussions.'],
        ];

        foreach ($messages as [$user, $content]) {
            $this->actingAs($user)
                ->postJson("/api/conversations/{$conversation->id}/messages", [
                    'content' => $content
                ])
                ->assertSuccessful();
        }

        // Verify all messages exist
        expect($conversation->messages()->count())->toBe(3);

        // Verify unread counts (each user has unread from others)
        expect($this->author->getUnreadMessagesCount())->toBe(2);
        expect($participant1->getUnreadMessagesCount())->toBe(2);
        expect($participant2->getUnreadMessagesCount())->toBe(2);
    });

    it('tests privacy and authorization throughout workflow', function () {
        $intruder = User::factory()->create(['email_verified_at' => now()]);

        // Create private conversation between author and friend
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->author->id => ['joined_at' => now()],
            $this->friend->id => ['joined_at' => now()]
        ]);

        // Intruder tries to access conversation - should fail
        $this->actingAs($intruder)
            ->getJson("/api/conversations/{$conversation->id}")
            ->assertForbidden();

        // Intruder tries to send message - should fail
        $this->actingAs($intruder)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Unauthorized message'
            ])
            ->assertForbidden();

        // Friend sends legitimate message
        $this->actingAs($this->friend)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Legitimate message'
            ])
            ->assertSuccessful();

        $message = Message::where('content', 'Legitimate message')->first();

        // Intruder tries to edit friend's message - should fail
        $this->actingAs($intruder)
            ->patchJson("/api/messages/{$message->id}", [
                'content' => 'Hacked message'
            ])
            ->assertForbidden();

        // Friend can edit their own message
        $this->actingAs($this->friend)
            ->patchJson("/api/messages/{$message->id}", [
                'content' => 'Edited legitimate message'
            ])
            ->assertSuccessful();

        $message->refresh();
        expect($message->content)->toBe('Edited legitimate message');
    });
});
