<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Recurring trip definition — weekly or monthly cadence with admin-configured
 * pricing/deduction defaults (agent.md Section 3).
 */
class TripSeries extends Model
{
    use HasFactory;

    protected $table = 'trip_series';

    protected $fillable = [
        'title',
        'description',
        'cadence',
        'day_of_week',
        'day_of_month',
        'default_capacity',
        'default_base_price',
        'default_accommodation_cost',
        'default_feeding_cost',
        'default_food_deduction_type',
        'default_food_deduction_value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'day_of_week' => 'integer',
            'day_of_month' => 'integer',
            'default_capacity' => 'integer',
            'default_base_price' => 'decimal:2',
            'default_accommodation_cost' => 'decimal:2',
            'default_feeding_cost' => 'decimal:2',
            'default_food_deduction_value' => 'decimal:2',
        ];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
