<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Bookings with pricing snapshots taken at booking time so historical
     * bookings remain accurate if the trip is later edited
     * (agent.md Sections 6 & 7). stripe_verified is only ever set via a
     * signed Stripe webhook — never from a frontend redirect (Section 11).
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending_payment', 'confirmed', 'cancelled'])
                ->default('pending_payment');

            // --- Fee snapshot (agent.md Section 6) ---
            $table->decimal('base_price_snapshot', 10, 2);
            $table->boolean('opted_out_of_food')->default(false);
            $table->enum('food_deduction_type_snapshot', ['flat', 'percentage'])
                ->nullable();
            $table->decimal('food_deduction_value_snapshot', 10, 2)->nullable();
            // Computed at booking time via Booking::calculateFinalPrice();
            // this is the amount actually charged via Stripe.
            $table->decimal('final_price', 10, 2);

            // --- Stripe ---
            $table->string('stripe_payment_intent_id')->nullable()->index();
            $table->string('stripe_session_id')->nullable()->index();
            $table->boolean('stripe_verified')->default(false);

            // --- Refunds (admin-triggered only) ---
            $table->boolean('refund_issued')->default(false);
            $table->timestamp('refunded_at')->nullable();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
