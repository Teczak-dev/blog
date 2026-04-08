<?php

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a private conversation from web create route', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $response = $this->actingAs($sender)
        ->post(route('conversations.create'), [
            'user_id' => $recipient->id,
        ]);

    $conversation = Conversation::query()->first();

    $response->assertRedirect(route('conversations.show', $conversation));

    expect($conversation)->not->toBeNull();
    expect($conversation->type)->toBe('private');

    $this->assertDatabaseHas('conversation_participants', [
        'conversation_id' => $conversation->id,
        'user_id' => $sender->id,
    ]);

    $this->assertDatabaseHas('conversation_participants', [
        'conversation_id' => $conversation->id,
        'user_id' => $recipient->id,
    ]);
});

it('reuses existing private conversation from web create route', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();

    $conversation = Conversation::createPrivate($sender, $recipient);

    $response = $this->actingAs($sender)
        ->post(route('conversations.create'), [
            'user_id' => $recipient->id,
        ]);

    $response->assertRedirect(route('conversations.show', $conversation));
    expect(Conversation::query()->count())->toBe(1);
});
