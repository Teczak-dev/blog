<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/hello-world/{name}', [HelloController::class, 'index']);

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

Route::middleware('auth')->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
});

Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');

// User profile routes
// User profile and discovery routes  
Route::get('/users', [\App\Http\Controllers\UserProfileController::class, 'index'])->name('users.index');
Route::get('/users/{user}', [\App\Http\Controllers\UserProfileController::class, 'show'])->name('users.profile');
Route::get('/users/{user}/followers', [\App\Http\Controllers\UserProfileController::class, 'followers'])->name('users.followers');
Route::get('/users/{user}/following', [\App\Http\Controllers\UserProfileController::class, 'following'])->name('users.following');
Route::get('/users/{user}/friends', [\App\Http\Controllers\UserProfileController::class, 'friends'])->name('users.friends');

Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::get('/posts/{post}/comments/load-more', [CommentController::class, 'loadMore'])->name('comments.load-more');
Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');

Route::middleware('auth')->group(function () {
    Route::post('/comments/{comment}/vote', [\App\Http\Controllers\CommentVoteController::class, 'vote'])->name('comments.vote');
    
    // Follow system routes
    Route::post('/users/{user}/follow', [\App\Http\Controllers\FollowController::class, 'toggle'])->name('users.follow');
    Route::post('/users/{user}/unfollow', [\App\Http\Controllers\FollowController::class, 'toggle'])->name('users.unfollow');
    Route::get('/users/{user}/followers', [\App\Http\Controllers\FollowController::class, 'followers'])->name('users.followers');
    Route::get('/users/{user}/following', [\App\Http\Controllers\FollowController::class, 'following'])->name('users.following');
    Route::get('/users/{user}/follow-status', [\App\Http\Controllers\FollowController::class, 'status'])->name('users.follow-status');
    Route::get('/follow/suggestions', [\App\Http\Controllers\FollowController::class, 'suggestions'])->name('follow.suggestions');
    
    // Friendship system routes
    Route::get('/friends', [\App\Http\Controllers\FriendshipController::class, 'showWebInterface'])->name('friends.index');
    Route::post('/users/{user}/friend-request', [\App\Http\Controllers\FriendshipController::class, 'sendRequest'])->name('friends.request');
    Route::post('/friendships/{friendship}/accept', [\App\Http\Controllers\FriendshipController::class, 'accept'])->name('friends.accept');
    Route::post('/friendships/{friendship}/reject', [\App\Http\Controllers\FriendshipController::class, 'reject'])->name('friends.reject');
    Route::delete('/friendships/{friendship}/cancel', [\App\Http\Controllers\FriendshipController::class, 'cancel'])->name('friends.cancel');
    Route::delete('/users/{user}/friend', [\App\Http\Controllers\FriendshipController::class, 'remove'])->name('friends.remove');
    Route::get('/friend-requests', [\App\Http\Controllers\FriendshipController::class, 'pendingRequests'])->name('friends.requests');
    
    // Conversation routes
    Route::get('/conversations', [\App\Http\Controllers\ConversationController::class, 'showWebInterface'])->name('conversations.index');
    Route::post('/conversations/create', [\App\Http\Controllers\ConversationController::class, 'webCreate'])->name('conversations.create');
    Route::get('/conversations/{conversation}', [\App\Http\Controllers\ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/mark-read', [\App\Http\Controllers\ConversationController::class, 'markAsRead'])->name('conversations.mark-read');
    
    // API conversation routes
    Route::get('/api/conversations', [\App\Http\Controllers\ConversationController::class, 'index'])->name('api.conversations.index');
    Route::post('/api/conversations/private/{user}', [\App\Http\Controllers\ConversationController::class, 'createPrivate'])->name('api.conversations.create-private');
    Route::delete('/conversations/{conversation}/leave', [\App\Http\Controllers\ConversationController::class, 'leave'])->name('conversations.leave');
    
    // Message routes
    Route::get('/conversations/{conversation}/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::post('/conversations/{conversation}/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::put('/messages/{message}', [\App\Http\Controllers\MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [\App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{message}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.read');
    Route::get('/messages/unread-count', [\App\Http\Controllers\MessageController::class, 'unreadCount'])->name('messages.unread-count');
});

require __DIR__.'/auth.php';
