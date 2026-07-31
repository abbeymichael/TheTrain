<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Logistics / care-context profile only — no matching-oriented fields
     * (agent.md Section 7).
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            // Short "what brings you here" note — private to admin/specialists only.
            $table->text('bio')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            // Dynamic dietary restriction options (JSON array of option keys).
            $table->json('dietary_restrictions')->nullable();
            $table->text('allergies')->nullable();
            $table->text('mobility_or_accessibility_needs')->nullable();
            // Never shown publicly or to other participants (agent.md Section 9).
            $table->string('profile_visibility')->default('private');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
