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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Who did it (null for system/guest actions).
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // What it was done to — polymorphic so it covers Post, Comment, User, Category, ...
            $table->nullableMorphs('subject');

            // Short machine-readable action key, e.g. "post.published", "user.promoted".
            $table->string('action');

            // Human-readable one-liner for the admin activity feed.
            $table->string('description');

            // Optional extra context (e.g. old/new role, changed fields).
            $table->json('properties')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
