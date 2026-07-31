<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Individual bookable trip instances — generated from a series or created
     * standalone (trip_series_id nullable). Pricing is all-inclusive with an
     * informational accommodation/feeding breakdown and a per-trip food
     * opt-out deduction rule (agent.md Sections 3 & 6).
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            // Null for one-off trips.
            $table->foreignId('trip_series_id')
                ->nullable()
                ->constrained('trip_series')
                ->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->string('city')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->unsignedInteger('capacity');
            // Total inclusive price (trip + accommodation + feeding).
            $table->decimal('base_price', 10, 2);
            // Informational breakdown components of base_price.
            $table->decimal('accommodation_cost', 10, 2)->default(0);
            $table->decimal('feeding_cost', 10, 2)->default(0);
            // Food opt-out deduction rule — can override the series default.
            $table->enum('food_deduction_type', ['flat', 'percentage'])
                ->default('flat');
            // Flat currency amount OR percentage (0–100), per food_deduction_type.
            $table->decimal('food_deduction_value', 10, 2)->default(0);
            $table->enum('status', ['draft', 'open', 'closed', 'completed'])
                ->default('draft');
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
