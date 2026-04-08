<?php

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Friendship System', function () {
    beforeEach(function () {
        $this->user1 = User::factory()->create(['name' => 'John Doe']);
        $this->user2 = User::factory()->create(['name' => 'Jane Smith']);
        $this->user3 = User::factory()->create(['name' => 'Bob Wilson']);
    });

    it('allows sending friend requests', function () {
        $this->actingAs($this->user1)
            ->postJson("/api/friends/request/{$this->user2->id}")
            ->assertSuccessful()
            ->assertJson(['status' => 'request_sent']);

        $this->assertDatabaseHas('friendships', [
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);
    });

    it('allows accepting friend requests', function () {
        // Create pending request
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user2)
            ->postJson("/api/friends/accept/{$this->user1->id}")
            ->assertSuccessful()
            ->assertJson(['status' => 'accepted']);

        $this->assertDatabaseHas('friendships', [
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'accepted',
        ]);
    });

    it('allows rejecting friend requests', function () {
        // Create pending request
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user2)
            ->postJson("/api/friends/reject/{$this->user1->id}")
            ->assertSuccessful()
            ->assertJson(['status' => 'rejected']);

        $this->assertDatabaseHas('friendships', [
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'rejected',
        ]);
    });

    it('allows blocking users', function () {
        $this->actingAs($this->user1)
            ->postJson("/api/friends/block/{$this->user2->id}")
            ->assertSuccessful()
            ->assertJson(['status' => 'blocked']);

        $this->assertDatabaseHas('friendships', [
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'blocked',
        ]);
    });

    it('allows unblocking users', function () {
        // Create blocked friendship
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'blocked',
        ]);

        $this->actingAs($this->user1)
            ->deleteJson("/api/friends/block/{$this->user2->id}")
            ->assertSuccessful()
            ->assertJson(['status' => 'unblocked']);

        $this->assertDatabaseMissing('friendships', [
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
        ]);
    });

    it('prevents sending duplicate friend requests', function () {
        // Send first request
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);

        // Try to send another
        $this->actingAs($this->user1)
            ->postJson("/api/friends/request/{$this->user2->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id']);
    });

    it('prevents users from friending themselves', function () {
        $this->actingAs($this->user1)
            ->postJson("/api/friends/request/{$this->user1->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id']);
    });

    it('shows pending requests correctly', function () {
        // User1 sends request to User2
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user2)
            ->getJson('/api/friends/pending')
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $this->user1->id]);
    });

    it('shows friends list correctly', function () {
        // Create accepted friendship
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'accepted',
        ]);

        $this->actingAs($this->user1)
            ->getJson('/api/friends')
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $this->user2->id]);

        $this->actingAs($this->user2)
            ->getJson('/api/friends')
            ->assertSuccessful()
            ->assertJsonFragment(['id' => $this->user1->id]);
    });

    it('counts pending friend requests correctly', function () {
        // Create multiple pending requests
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);

        Friendship::create([
            'requester_id' => $this->user3->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);

        expect($this->user2->getPendingFriendRequestsCount())->toBe(2);
    });

    it('shows friendship status correctly in UI', function () {
        $this->actingAs($this->user1)
            ->get('/friends')
            ->assertSuccessful()
            ->assertSee('Znajomi');

        // After sending request
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->user1)
            ->get('/friends')
            ->assertSuccessful();
    });

    it('handles friend removal', function () {
        // Create accepted friendship
        $friendship = Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'accepted',
        ]);

        $this->actingAs($this->user1)
            ->deleteJson("/api/friends/{$this->user2->id}")
            ->assertSuccessful()
            ->assertJson(['status' => 'removed']);

        $this->assertDatabaseMissing('friendships', [
            'id' => $friendship->id,
        ]);
    });
});

describe('Friendship Model Logic', function () {
    beforeEach(function () {
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
    });

    it('prevents self-friendship in model', function () {
        $friendship = new Friendship([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user1->id,
            'status' => 'pending',
        ]);

        expect(fn() => $friendship->save())
            ->toThrow(\Exception::class, 'Users cannot be friends with themselves');
    });

    it('prevents duplicate friendships in model', function () {
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);

        $duplicate = new Friendship([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'pending',
        ]);

        expect(fn() => $duplicate->save())
            ->toThrow(\Exception::class, 'Friendship already exists');
    });

    it('has correct relationship methods', function () {
        $friendship = Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'accepted',
        ]);

        expect($friendship->requester->id)->toBe($this->user1->id);
        expect($friendship->addressee->id)->toBe($this->user2->id);
    });

    it('has working getFriends method', function () {
        Friendship::create([
            'requester_id' => $this->user1->id,
            'addressee_id' => $this->user2->id,
            'status' => 'accepted',
        ]);

        $friends = $this->user1->getFriends();
        expect($friends)->toHaveCount(1);
        expect($friends->first()->id)->toBe($this->user2->id);

        $friends = $this->user2->getFriends();
        expect($friends)->toHaveCount(1);
        expect($friends->first()->id)->toBe($this->user1->id);
    });
});
