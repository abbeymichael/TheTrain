<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Recurring trip definition with admin-configured cadence and pricing
     * defaults (agent.md Section 3).
     */
    public function up(): void
    {
        Schema::create('trip_series', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('cadence', ['weekly', 'monthly']);
            // Used when cadence = weekly (0 = Sunday … 6 = Saturday).
            $table->unsignedTinyInteger('day_of_week')->nullable();
            // Used when cadence = monthly (1–31).
            $table->unsignedTinyInteger('day_of_month')->nullable();
            $table->unsignedInteger('default_capacity')->default(12);
            $table->decimal('default_base_price', 10, 2)->default(0);
            // Airbnb cost baked into the base price.
            $table->decimal('default_accommodation_cost', 10, 2)->default(0);
            // Feeding cost baked into the base price.
            $table->decimal('default_feeding_cost', 10, 2)->default(0);
            $table->enum('default_food_deduction_type', ['flat', 'percentage'])
                ->default('flat');
            $table->decimal('default_food_deduction_value', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_series');
    }
};
