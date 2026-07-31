<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InvalidArgumentException;

/**
 * Booking with pricing snapshots taken at booking time (agent.md Sections 6 & 7).
 *
 * final_price (post food-deduction) is the amount actually charged via
 * Stripe — never base_price_snapshot. stripe_verified is set only via a
 * signed Stripe webhook (Section 11).
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'status',
        'base_price_snapshot',
        'opted_out_of_food',
        'food_deduction_type_snapshot',
        'food_deduction_value_snapshot',
        'final_price',
        'stripe_payment_intent_id',
        'stripe_session_id',
        'stripe_verified',
        'refund_issued',
        'refunded_at',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'opted_out_of_food' => 'boolean',
            'stripe_verified' => 'boolean',
            'refund_issued' => 'boolean',
            'base_price_snapshot' => 'decimal:2',
            'food_deduction_value_snapshot' => 'decimal:2',
            'final_price' => 'decimal:2',
            'refunded_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /** Challenges selected at booking time; is_primary routes the specialist track. */
    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'booking_challenges')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Canonical payment check (agent.md Section 11).
     */
    public function isPaymentVerified(): bool
    {
        return $this->stripe_verified && $this->status === 'confirmed';
    }

    /**
     * Single source of truth for the food opt-out deduction math
     * (agent.md Section 6). Never recompute this ad hoc in a view.
     *
     * - opted in (default)          → base price
     * - opted out, flat deduction   → base price − value
     * - opted out, percentage       → base price − (base price × value / 100)
     *
     * The result is never negative.
     */
    public static function calculateFinalPrice(
        float|string $basePrice,
        bool $optedOutOfFood,
        ?string $deductionType = null,
        float|string|null $deductionValue = null,
    ): float {
        $basePrice = (float) $basePrice;

        if (! $optedOutOfFood) {
            return round($basePrice, 2);
        }

        $deductionValue = (float) ($deductionValue ?? 0);

        $deduction = match ($deductionType) {
            'flat' => $deductionValue,
            'percentage' => $basePrice * ($deductionValue / 100),
            default => throw new InvalidArgumentException(
                'A food deduction type (flat|percentage) is required when opting out of food.'
            ),
        };

        return round(max(0, $basePrice - $deduction), 2);
    }

    /**
     * Recalculate this booking's final price from its stored snapshot fields.
     */
    public function recalculateFinalPrice(): float
    {
        $this->final_price = static::calculateFinalPrice(
            $this->base_price_snapshot,
            $this->opted_out_of_food,
            $this->food_deduction_type_snapshot,
            $this->food_deduction_value_snapshot,
        );

        return (float) $this->final_price;
    }
}
