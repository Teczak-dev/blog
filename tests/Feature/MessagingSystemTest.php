<?php

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Messaging System', function () {
    beforeEach(function () {
        $this->user1 = User::factory()->create(['name' => 'John Doe']);
        $this->user2 = User::factory()->create(['name' => 'Jane Smith']);
        $this->user3 = User::factory()->create(['name' => 'Bob Wilson']);
    });

    it('creates conversations between users', function () {
        $this->actingAs($this->user1)
            ->postJson('/api/conversations', [
                'participants' => [$this->user2->id],
                'type' => 'private'
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'type', 'participants']);

        $this->assertDatabaseHas('conversations', [
            'type' => 'private',
        ]);

        $this->assertDatabaseHas('conversation_participants', [
            'user_id' => $this->user1->id,
        ]);

        $this->assertDatabaseHas('conversation_participants', [
            'user_id' => $this->user2->id,
        ]);
    });

    it('sends messages in conversations', function () {
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        $this->actingAs($this->user1)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Hello there!'
            ])
            ->assertSuccessful()
            ->assertJsonFragment(['content' => 'Hello there!']);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->user1->id,
            'content' => 'Hello there!',
        ]);
    });

    it('prevents unauthorized access to conversations', function () {
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        // User3 trying to access conversation they're not part of
        $this->actingAs($this->user3)
            ->getJson("/api/conversations/{$conversation->id}")
            ->assertForbidden();

        $this->actingAs($this->user3)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'Unauthorized message'
            ])
            ->assertForbidden();
    });

    it('lists user conversations correctly', function () {
        // Create conversation between user1 and user2
        $conversation1 = Conversation::create(['type' => 'private']);
        $conversation1->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        // Create conversation between user1 and user3
        $conversation2 = Conversation::create(['type' => 'private']);
        $conversation2->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user3->id => ['joined_at' => now()]
        ]);

        $this->actingAs($this->user1)
            ->getJson('/conversations')
            ->assertSuccessful()
            ->assertSee($this->user2->name)
            ->assertSee($this->user3->name);
    });

    it('shows unread message counts', function () {
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        // User2 sends message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->user2->id,
            'content' => 'Unread message',
        ]);

        expect($this->user1->getUnreadMessagesCount())->toBe(1);
        expect($this->user2->getUnreadMessagesCount())->toBe(0); // sender doesn't count own messages
    });

    it('marks messages as read', function () {
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        // User2 sends message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->user2->id,
            'content' => 'Test message',
        ]);

        // User1 reads the conversation
        $this->actingAs($this->user1)
            ->patchJson("/api/conversations/{$conversation->id}/read")
            ->assertSuccessful();

        // Check that last_read_at is updated
        $participant = $conversation->participants()
            ->where('user_id', $this->user1->id)
            ->first();

        expect($participant->pivot->last_read_at)->not->toBeNull();
    });

    it('allows message editing by sender', function () {
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->user1->id,
            'content' => 'Original message',
        ]);

        $this->actingAs($this->user1)
            ->patchJson("/api/messages/{$message->id}", [
                'content' => 'Edited message'
            ])
            ->assertSuccessful()
            ->assertJsonFragment(['content' => 'Edited message']);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'content' => 'Edited message',
        ]);
    });

    it('prevents editing messages by non-sender', function () {
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->user1->id,
            'content' => 'Original message',
        ]);

        $this->actingAs($this->user2)
            ->patchJson("/api/messages/{$message->id}", [
                'content' => 'Unauthorized edit'
            ])
            ->assertForbidden();
    });

    it('allows message deletion by sender', function () {
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->user1->id,
            'content' => 'Message to delete',
        ]);

        $this->actingAs($this->user1)
            ->deleteJson("/api/messages/{$message->id}")
            ->assertSuccessful();

        $this->assertSoftDeleted('messages', [
            'id' => $message->id,
        ]);
    });

    it('finds or creates private conversations', function () {
        // First request should create conversation
        $this->actingAs($this->user1)
            ->postJson('/api/conversations/private', [
                'user_id' => $this->user2->id
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'type']);

        $conversationId = Conversation::where('type', 'private')->first()->id;

        // Second request should return existing conversation
        $this->actingAs($this->user1)
            ->postJson('/api/conversations/private', [
                'user_id' => $this->user2->id
            ])
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $conversationId]);

        // Should only have one conversation
        expect(Conversation::count())->toBe(1);
    });

    it('updates last_message_at when sending messages', function () {
        $conversation = Conversation::create(['type' => 'private']);
        $conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);

        $oldTimestamp = $conversation->last_message_at;

        // Send message
        $this->actingAs($this->user1)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'content' => 'New message'
            ])
            ->assertSuccessful();

        $conversation->refresh();
        expect($conversation->last_message_at)->toBeGreaterThan($oldTimestamp);
    });
});

describe('Message Model', function () {
    beforeEach(function () {
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
        $this->conversation = Conversation::create(['type' => 'private']);
        $this->conversation->participants()->attach([
            $this->user1->id => ['joined_at' => now()],
            $this->user2->id => ['joined_at' => now()]
        ]);
    });

    it('has correct relationships', function () {
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user1->id,
            'content' => 'Test message',
        ]);

        expect($message->conversation->id)->toBe($this->conversation->id);
        expect($message->sender->id)->toBe($this->user1->id);
    });

    it('uses soft deletes', function () {
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user1->id,
            'content' => 'Test message',
        ]);

        $message->delete();

        $this->assertSoftDeleted('messages', [
            'id' => $message->id,
        ]);

        // Should still be accessible via withTrashed()
        expect(Message::withTrashed()->find($message->id))->not->toBeNull();
    });
});
