<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * [DYNAMIC] admin-managed challenge category (agent.md Section 4).
 *
 * Never hardcode the list in code — read from this table.
 */
class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_sensitive',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_sensitive' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Challenge $challenge): void {
            if (empty($challenge->slug)) {
                $challenge->slug = Str::slug($challenge->name);
            }
        });
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    /** Trips that support this challenge track. */
    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class, 'trip_challenges')
            ->withTimestamps();
    }

    /** Specialists who cover this challenge category. */
    public function specialists(): BelongsToMany
    {
        return $this->belongsToMany(Specialist::class, 'specialist_challenges')
            ->withTimestamps();
    }

    /** Bookings that selected this challenge. */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_challenges')
            ->withPivot('is_primary')
            ->withTimestamps();
    }
}
