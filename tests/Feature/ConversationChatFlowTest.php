<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('loads conversation page for participant without rendering route errors', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $conversation = Conversation::createPrivate($user1, $user2);

    $this->actingAs($user1)
        ->get(route('conversations.show', $conversation))
        ->assertOk()
        ->assertSee('messageForm', false);
});

it('allows participant to send a message to conversation', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $conversation = Conversation::createPrivate($user1, $user2);

    $this->actingAs($user1)
        ->post(route('messages.store', $conversation), [
            'content' => 'Czesc, test wiadomosci',
        ], ['Accept' => 'application/json'])
        ->assertCreated()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('messages', [
        'conversation_id' => $conversation->id,
        'user_id' => $user1->id,
        'content' => 'Czesc, test wiadomosci',
    ]);
});

