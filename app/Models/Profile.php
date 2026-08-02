<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Logistics / care-context profile only — no matching-oriented fields
 * (agent.md Section 7). Visibility is private by default; profiles are
 * never shown publicly or to other participants (Section 9) — there is
 * no "public" visibility option at all.
 */
class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'bio',
        'emergency_contact_name',
        'emergency_contact_phone',
        'dietary_restrictions',
        'allergies',
        'mobility_or_accessibility_needs',
        'profile_visibility',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'dietary_restrictions' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
