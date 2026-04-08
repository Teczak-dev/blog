<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_new_posts')->default(true)->after('email_notifications');
            $table->boolean('notify_messages')->default(true)->after('notify_new_posts');
            $table->boolean('notify_friend_requests')->default(true)->after('notify_messages');
            $table->json('muted_users')->nullable()->after('notify_friend_requests'); // Array of user IDs to mute
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notify_new_posts', 'notify_messages', 'notify_friend_requests', 'muted_users']);
        });
    }
};
