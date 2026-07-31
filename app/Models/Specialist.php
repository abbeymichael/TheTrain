<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Specialist profile (agent.md Section 5). The owning user row carries
 * role = 'specialist'. Assignment to trips/challenge tracks is entirely
 * admin-driven — there is no algorithmic matching.
 */
class Specialist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'credentials',
        'bio',
        'photo_path',
        'years_experience',
        'is_verified',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'years_experience' => 'integer',
        ];
    }

    /** Verified + active specialists are eligible for trip assignment. */
    #[Scope]
    protected function assignable(Builder $query): void
    {
        $query->where('is_verified', true)->where('status', 'active');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Challenge categories this specialist covers. */
    public function challenges(): BelongsToMany
    {
        return $this->belongsToMany(Challenge::class, 'specialist_challenges')
            ->withTimestamps();
    }

    /** Trips this specialist is assigned to (with challenge track + role note). */
    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class, 'trip_specialists')
            ->withPivot(['challenge_id', 'role_note'])
            ->withTimestamps();
    }
}
