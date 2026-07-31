<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Pivot: which specialist runs which challenge track on a given trip.
     * Assignment is entirely admin-driven (agent.md Section 5).
     */
    public function up(): void
    {
        Schema::create('trip_specialists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            // e.g. "Lead facilitator", "Co-facilitator".
            $table->string('role_note')->nullable();
            $table->timestamps();

            $table->unique(
                ['trip_id', 'specialist_id', 'challenge_id'],
                'trip_specialists_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_specialists');
    }
};
