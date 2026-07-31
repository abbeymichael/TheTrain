<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Specialist profile table (agent.md Section 5). The owning user row has
     * role = 'specialist'. Status uses the specialist lifecycle, which is
     * distinct from users.status (agent.md Section 2).
     */
    public function up(): void
    {
        Schema::create('specialists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name');
            $table->string('credentials')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('years_experience')->nullable();
            // Admin-toggled after credential review.
            $table->boolean('is_verified')->default(false);
            $table->enum('status', [
                'pending_verification',
                'verified',
                'active',
                'inactive',
                'rejected',
            ])->default('pending_verification');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialists');
    }
};
