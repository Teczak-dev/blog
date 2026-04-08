<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Follow;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Follow System', function () {
    beforeEach(function () {
        $this->user1 = User::factory()->create(['name' => 'John Doe']);
        $this->user2 = User::factory()->create(['name' => 'Jane Smith']);
        $this->user3 = User::factory()->create(['name' => 'Bob Wilson']);
    });

    it('allows a user to follow another user', function () {
        $this->actingAs($this->user1)
            ->postJson("/users/{$this->user2->id}/follow")
            ->assertSuccessful()
            ->assertJson(['status' => 'following']);

        expect($this->user1->isFollowing($this->user2))->toBeTrue();
        
        $this->assertDatabaseHas('follows', [
            'follower_id' => $this->user1->id,
            'followed_id' => $this->user2->id,
        ]);
    });

    it('allows a user to unfollow another user', function () {
        // First follow
        $this->user1->following()->attach($this->user2->id);

        $this->actingAs($this->user1)
            ->postJson("/users/{$this->user2->id}/follow")
            ->assertSuccessful()
            ->assertJson(['status' => 'not_following']);

        expect($this->user1->isFollowing($this->user2))->toBeFalse();
        
        $this->assertDatabaseMissing('follows', [
            'follower_id' => $this->user1->id,
            'followed_id' => $this->user2->id,
        ]);
    });

    it('prevents users from following themselves', function () {
        $this->actingAs($this->user1)
            ->postJson("/users/{$this->user1->id}/follow")
            ->assertUnprocessable();
    });

    it('requires authentication to follow users', function () {
        $this->postJson("/users/{$this->user2->id}/follow")
            ->assertRedirect('/login');
    });

    it('returns follow status correctly', function () {
        // Not following initially
        $this->actingAs($this->user1)
            ->getJson("/users/{$this->user2->id}/follow-status")
            ->assertSuccessful()
            ->assertJson(['isFollowing' => false]);

        // After following
        $this->user1->following()->attach($this->user2->id);
        
        $this->actingAs($this->user1)
            ->getJson("/users/{$this->user2->id}/follow-status")
            ->assertSuccessful()
            ->assertJson(['isFollowing' => true]);
    });

    it('shows followed users in suggestions correctly', function () {
        // User1 follows User2
        $this->user1->following()->attach($this->user2->id);
        
        $this->actingAs($this->user1)
            ->getJson('/follow/suggestions')
            ->assertSuccessful()
            ->assertJsonMissing(['id' => $this->user2->id]) // Should not suggest already followed users
            ->assertJsonFragment(['id' => $this->user3->id]); // Should suggest unfollowed users
    });

    it('filters posts by followed users only', function () {
        // Create posts
        $post1 = Post::factory()->create(['user_id' => $this->user2->id, 'title' => 'Post by followed user']);
        $post2 = Post::factory()->create(['user_id' => $this->user3->id, 'title' => 'Post by unfollowed user']);
        
        // User1 follows User2
        $this->user1->following()->attach($this->user2->id);
        
        $this->actingAs($this->user1)
            ->get('/posts?filter=following')
            ->assertSuccessful()
            ->assertSee($post1->title)
            ->assertDontSee($post2->title);
    });

    it('shows correct follower and following counts', function () {
        // User1 and User3 follow User2
        $this->user1->following()->attach($this->user2->id);
        $this->user3->following()->attach($this->user2->id);
        
        // User2 follows User3
        $this->user2->following()->attach($this->user3->id);

        // Check User2's counts
        expect($this->user2->followers()->count())->toBe(2); // followed by User1 and User3
        expect($this->user2->following()->count())->toBe(1); // follows User3
    });

    it('handles bulk following operations', function () {
        $users = User::factory()->count(5)->create();
        $userIds = $users->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            $this->actingAs($this->user1)
                ->postJson("/users/{$userId}/follow")
                ->assertSuccessful();
        }

        expect($this->user1->following()->count())->toBe(5);
    });

    it('shows follow button state correctly in UI', function () {
        $post = Post::factory()->create(['user_id' => $this->user2->id]);

        // Before following
        $this->actingAs($this->user1)
            ->get("/posts/{$post->id}")
            ->assertSuccessful()
            ->assertSee('Obserwuj');

        // After following
        $this->user1->following()->attach($this->user2->id);
        
        $this->actingAs($this->user1)
            ->get("/posts/{$post->id}")
            ->assertSuccessful()
            ->assertSee('Obserwujesz');
    });
});

describe('Follow Model Relationships', function () {
    beforeEach(function () {
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
    });

    it('has correct relationship methods', function () {
        $this->user1->following()->attach($this->user2->id);

        expect($this->user1->following)->toHaveCount(1);
        expect($this->user1->following->first()->id)->toBe($this->user2->id);
        
        expect($this->user2->followers)->toHaveCount(1);
        expect($this->user2->followers->first()->id)->toBe($this->user1->id);
    });

    it('has working isFollowing method', function () {
        expect($this->user1->isFollowing($this->user2))->toBeFalse();
        
        $this->user1->following()->attach($this->user2->id);
        
        expect($this->user1->isFollowing($this->user2))->toBeTrue();
    });
});
