<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('updates last_read_at on conversation participants pivot', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $conversation = Conversation::createPrivate($user1, $user2);

    $conversation->participants()->updateExistingPivot($user1->id, [
        'last_read_at' => null,
    ]);

    $conversation->markAsReadForUser($user1);

    $this->assertDatabaseHas('conversation_participants', [
        'conversation_id' => $conversation->id,
        'user_id' => $user1->id,
    ]);

    expect(
        $conversation->participants()
            ->where('users.id', $user1->id)
            ->first()
            ->pivot
            ->last_read_at
    )->not->toBeNull();
});

it('updates left_at on conversation participants pivot when removing participant', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $conversation = Conversation::createPrivate($user1, $user2);

    $conversation->removeParticipant($user1);

    expect(
        $conversation->participants()
            ->where('users.id', $user1->id)
            ->first()
            ->pivot
            ->left_at
    )->not->toBeNull();
});
