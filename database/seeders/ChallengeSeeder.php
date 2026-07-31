<?php

namespace Database\Seeders;

use App\Models\Challenge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the initial dynamic challenge list (agent.md Sections 4 & 14).
 *
 * This list is admin-editable at runtime — the seeder only provides the
 * starting set; admins manage it afterwards via Admin\ChallengesManager.
 */
class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $challenges = [
            [
                'name' => 'Divorce',
                'description' => 'Navigating separation or divorce and rebuilding your life.',
                'is_sensitive' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'Depression',
                'description' => 'Living with depression and finding supportive community.',
                'is_sensitive' => false,
                'sort_order' => 20,
            ],
            [
                'name' => 'Loneliness',
                'description' => 'Coping with isolation and building meaningful connection.',
                'is_sensitive' => false,
                'sort_order' => 30,
            ],
            [
                'name' => 'Grief & Loss',
                'description' => 'Processing bereavement and major life losses.',
                'is_sensitive' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'Addiction Recovery',
                'description' => 'Support while in recovery from substance or behavioral addiction.',
                'is_sensitive' => true,
                'sort_order' => 50,
            ],
            [
                'name' => 'Anxiety',
                'description' => 'Managing anxiety in a calm, guided retreat setting.',
                'is_sensitive' => false,
                'sort_order' => 60,
            ],
            [
                'name' => 'Burnout',
                'description' => 'Recovering from exhaustion and restoring balance.',
                'is_sensitive' => false,
                'sort_order' => 70,
            ],
            [
                'name' => 'Chronic Illness',
                'description' => 'Living with long-term health conditions with support.',
                'is_sensitive' => true,
                'sort_order' => 80,
            ],
            [
                'name' => 'Caregiver Fatigue',
                'description' => 'Rest and support for those caring for others.',
                'is_sensitive' => false,
                'sort_order' => 90,
            ],
        ];

        foreach ($challenges as $challenge) {
            Challenge::updateOrCreate(
                ['slug' => Str::slug($challenge['name'])],
                $challenge + ['is_active' => true],
            );
        }
    }
}
