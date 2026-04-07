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
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('post_id');
            $table->boolean('is_approved')->default(false)->after('content');
            $table->unsignedBigInteger('approved_by')->nullable()->after('is_approved');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['post_id', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['post_id', 'is_approved']);
            
            $table->dropColumn(['user_id', 'is_approved', 'approved_by', 'approved_at']);
        });
    }
};
