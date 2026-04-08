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
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('addressee_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'blocked'])->default('pending');
            $table->timestamps();
            
            // Prevent duplicate friendship requests
            $table->unique(['requester_id', 'addressee_id']);
            
            // Note: Self-friendship should be prevented in business logic
            
            // Indexes for performance
            $table->index('requester_id');
            $table->index('addressee_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
