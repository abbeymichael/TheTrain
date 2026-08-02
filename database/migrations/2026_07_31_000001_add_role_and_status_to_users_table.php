<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds role/status columns per agent.md Section 2 & 7.
     * role: user | specialist | admin
     * status: pending -> approved -> (booking-eligible) / rejected
     *         approved -> suspended -> reinstated -> approved
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'specialist', 'admin'])
                ->default('user')
                ->after('password');
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])
                ->default('pending')
                ->after('role');
            $table->string('phone')->nullable()->after('status');
            $table->timestamp('last_active_at')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'phone', 'last_active_at']);
        });
    }
};
