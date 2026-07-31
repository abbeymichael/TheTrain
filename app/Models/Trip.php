<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Individual bookable trip instance (agent.md Section 3).
 *
 * base_price is all-inclusive (trip + accommodation + feeding);
 * accommodation_cost / feeding_cost are informational breakdown components.
 * The food opt-out deduction rule can override the series default per trip.
 */
class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_series_id',
        'title',
        'description',
        'venue',
        'city',
        'start_date',
        'end_date',
        'capacity',
        'base_price',
        'accommodation_cost',
        'feeding_cost',
        'food_deduction_type',
        'food_deduction_value',
        'status',
        'cover_image',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'capacity' => 'integer',
            'base_price' => 'decimal:2',
            'accommodation_cost' => 'decimal:2',
            'feeding_cost' => 'decimal:2',
            'food_deduction_value' => 'decimal:2',
        ];
    }

    #[Scope]
    protected function open(Builder $query): void
    {
        $query->where('status', 'open');
    }

    #[Scope]
    protected function upcoming(Builder $query): void
    {
        $query->where('start_date', '>=', now())->orderBy('start_date');
    }

    public function tripSeries(): BelongsTo
    {
        return $this->belongsTo(TripSeries::class);
    }

    /** Challenge tracks this trip supports. */
    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'trip_challenges')
            ->withTimestamps();
    }

    /** Specialists assigned to this trip (with their challenge track + role note). */
    public function specialists(): BelongsToMany
    {
        return $this->belongsToMany(Specialist::class, 'trip_specialists')
            ->withPivot(['challenge_id', 'role_note'])
            ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Confirmed bookings only — used for capacity checks. */
    public function confirmedBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('status', 'confirmed');
    }

    public function hasAvailability(): bool
    {
        return $this->confirmedBookings()->count() < $this->capacity;
    }
}
