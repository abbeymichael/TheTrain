<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Platform account (agent.md Section 2 & 7).
 *
 * role: user | specialist | admin — custom role/permission layer lives on
 * the `roles` table (agent.md Section 2), this column is the coarse gate.
 * status drives the registration/approval/suspension lifecycle.
 */
#[Fillable(['name', 'email', 'password', 'role', 'status', 'phone', 'last_active_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_active_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Logistics / care-context profile (agent.md Section 7). */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /** Present when role = 'specialist' (agent.md Section 5). */
    public function specialist(): HasOne
    {
        return $this->hasOne(Specialist::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Booking-eligible per agent.md Section 2: approved users with a verified
     * email may book trips (payment handled separately via Stripe).
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Booking gate per agent.md Section 2: registered + verified email +
     * approved status. Stripe payment is checked separately per booking.
     */
    public function canBookTrips(): bool
    {
        return $this->email_verified_at !== null && $this->isApproved();
    }
}
