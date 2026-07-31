# TODO — TheTrain Platform

> Working plan derived from `agent.md`. Updated by AI agents as work progresses.
> Last updated: 2026-07-31

## ✅ Done (this session — 2026-07-31, `second-round` branch)

- [x] Read `agent.md` fully; picked up **database foundation** as this session's scope
      (agent.md Section 14, priorities 1–4 — everything else depends on it).
- [x] Migrations:
  - [x] `add_role_and_status_to_users_table` — `role`, `status`, `phone`, `last_active_at` (agent.md §2, §7)
  - [x] `create_profiles_table` — logistics/care-context fields only (§7; no matching fields)
  - [x] `create_challenges_table` — dynamic, admin-managed (§4)
  - [x] `create_trip_series_table` — weekly/monthly cadence + pricing/deduction defaults (§3)
  - [x] `create_trips_table` — bookable instances, pricing breakdown + food deduction rule (§3)
  - [x] `create_trip_challenges_table` — pivot, unique (trip_id, challenge_id) (§3)
  - [x] `create_specialists_table` — profile + verification status (§5)
  - [x] `create_specialist_challenges_table` — pivot (§5)
  - [x] `create_trip_specialists_table` — pivot with `role_note`, unique (trip, specialist, challenge) (§5)
  - [x] `create_bookings_table` — fee snapshot fields, Stripe fields, refund fields (§6, §7)
  - [x] `create_booking_challenges_table` — pivot with `is_primary` (§4)
- [x] Models with relationships: `Challenge`, `TripSeries`, `Trip`, `Specialist`, `Booking`;
      updated `User` (fillable, casts, relationships, `isApproved()` helper).
- [x] `Booking::calculateFinalPrice()` — single source of truth for flat/percentage
      food-deduction math (agent.md §6 requirement).
- [x] `ChallengeSeeder` — seeds initial dynamic challenge list
      (Divorce, Depression, Loneliness, Grief & Loss, Addiction Recovery, Anxiety, Burnout)
      with `is_sensitive` flags; wired into `DatabaseSeeder`.
- [x] `role:admin` / `role:specialist` middleware (`App\Http\Middleware\EnsureUserHasRole`)
      registered as `role` alias in `bootstrap/app.php` (agent.md §11 hard requirement).
- [x] Public pages converted from `tailwindhtml files/` into Livewire 4 components:
  - [x] `resources/css/app.css` — Tailwind 4 theme tokens (colors, fonts, sizes, spacing) matching the design palette.
  - [x] `resources/js/app.js` — public-layout progressive enhancements (navbar shadow, card hover).
  - [x] `App\Livewire\Public\HomePage` + view (`livewire/public/home-page.blade.php`) — hero, features, how it works, testimonials, CTA.
  - [x] `App\Livewire\Public\TripsList` + view — filterable upcoming trips by cadence, location, and challenge track.
  - [x] `App\Livewire\Public\TripShow` + view — single trip detail with challenge tracks, pricing breakdown, food opt-out, assigned specialists, and booking CTA.
  - [x] `resources/views/layouts/public.blade.php` — public nav + footer, Vite assets, Livewire styles/scripts.
- [x] `App\Livewire\Admin\ChallengesManager` + view — full CRUD for dynamic challenge list, including create/edit, toggle active, soft-delete guard, and search/pagination.
- [x] `resources/views/layouts/admin.blade.php` — admin sidebar layout with navigation, user card, and logout.
- [x] `routes/web.php` — wired public routes and role-protected `/admin/*` and `/specialist/*` placeholders; `role:admin` / `role:specialist` middleware applied per agent.md §11.

## 🔲 Next Up (agent.md §14, remaining priorities)

- [ ] Stripe webhook handler — `payment_intent.succeeded` / `checkout.session.completed`
      → `stripe_verified = true`, `status = confirmed` (§14.5). Requires `stripe/stripe-php`.
- [ ] `Admin\TripSeriesManager` (§10).
- [ ] `Admin\TripEditor` — pricing breakdown + food deduction rule config (§14.6).
- [ ] `Admin\TripSpecialistAssigner` (§14.7).
- [ ] `Specialist\TripRoster` — scoped to assigned challenge tracks only (§14.8).
- [ ] `Admin\RefundManager` — admin-triggered Stripe refunds only (§14.9).
- [ ] `last_active_at` tracking on auth events (§14.10).
- [ ] `dietary_restrictions` dynamic options table (§12.2) — currently JSON on profiles.
- [ ] Laravel Fortify install (auth + 2FA) — auth scaffolding not yet present.

## Notes / Decisions

- PHP is not installed in the current sandbox → code validated by static review only;
  run `php artisan migrate` + `php artisan db:seed` locally to verify.
- Enums kept as DB enums only for structural states (roles, statuses, cadence,
  deduction type); user-facing lists stay in tables (agent.md §12).
- `specialists.status` uses its own lifecycle enum (`pending_verification`, `verified`,
  `active`, `inactive`, `rejected`) per §2 — separate from `users.status`.
- Public images use Unsplash source URLs as placeholders; replace with production assets later.
- Auth routes (`/login`, `/register`) are temporary redirects until Laravel Fortify is installed.
