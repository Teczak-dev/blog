<?php

it('registers filament routes for all social resources', function () {
    expect(route('filament.admin.resources.follows.index', absolute: false))->toBe('/admin/follows');
    expect(route('filament.admin.resources.friendships.index', absolute: false))->toBe('/admin/friendships');
    expect(route('filament.admin.resources.conversations.index', absolute: false))->toBe('/admin/conversations');
    expect(route('filament.admin.resources.conversation-participants.index', absolute: false))->toBe('/admin/conversation-participants');
    expect(route('filament.admin.resources.messages.index', absolute: false))->toBe('/admin/messages');
    expect(route('filament.admin.resources.comment-votes.index', absolute: false))->toBe('/admin/comment-votes');
});
