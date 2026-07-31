# AGENT.md — TheTrain Platform: Full Scope & Architecture Reference

> **Purpose:** This document is the authoritative scope reference for every AI agent, developer, or contributor working on this codebase. Read this fully before making any changes. It describes what is being built, the current state of every system, what is intentionally dynamic/admin-configurable, and what must remain extensible.

---

## 1. WHAT IS THE TRAIN?

**TheTrain** is a scheduled, curated support-retreat and trip-booking platform. It is **not** a dating or matchmaking product, and it has **no membership tiers and no matching pool**.

- Users register (membership itself is **free** — no tiers, no gated access levels).
- Admin publishes a **schedule of trips/events**, run either **weekly or monthly or one off dated trips** (cadence is admin-defined per trip series, not hardcoded).
- When booking a trip, a user selects the **challenge(s) they are currently facing** — e.g. divorce, depression, loneliness, grief, addiction recovery, anxiety, burnout — from an admin-managed list.
- Admin brings on **specialists** (therapists, counselors, coaches, etc.) matched to the challenge categories, and assigns specialists to specific trips/sessions.
- **There is no matching between participants.** Specialists are matched to challenges/trips, not participants to each other.
- Every trip fee is **all-inclusive by default**: it bundles the base trip cost, **accommodation (Airbnb) fees**, and **feeding**.
- A user may **opt out of food**. When they do, the system applies a **deduction from the fee** — configurable per trip as either a **flat amount** or a **percentage** of the base fee.
- **Events must be booked and paid for via Stripe.** A booking is only valid once Stripe payment is verified.
- The entire platform is **admin-driven**: challenge categories, specialist assignments, trip schedules, pricing components, and food-deduction rules are all configurable — not hardcoded.

**Brand tone:** Calm, supportive, trustworthy. Think guided retreat / support community, not a marketplace or a dating app.

**Stack:**
- **Backend:** Laravel 13 (PHP 8.3), Livewire 4, Laravel Fortify (auth + 2FA)
- **Frontend:** Livewire 4 single-file components, Tailwind CSS
- **Build:** Vite 8
- **Payments:** Stripe (Checkout / Payment Intents)
- **DB:** SQLite (dev) — designed to be migrated to MySQL/PostgreSQL in production
- **Storage:** Laravel public disk for profile photos and trip cover images

---

## 2. USER ROLES & ACCOUNT STATES

### Roles (stored on `users.role`)
No separate `admins` table. Roles/permissions are custom-built — a `roles` table where each role is composed of modules (named after the models they manage) and operations on those models (`create`, `edit`, `approve`, `pay`, `assign_specialist`, etc.), joined via a `role_permission` pivot. Prefer a policy/gate layer over hardcoded `isAdmin()` / `isSpecialist()` checks.

| Role | Description |
|------|-------------|
| `user` | Standard participant booking trips |
| `specialist` | Professional brought on to run challenge-specific sessions on trips |
| `admin` | Full platform control |

> **Future:** A `coordinator` role (logistics-only, no clinical/admin access) is anticipated. Design with role extensibility in mind.

### User Status Flow (stored on `users.status`)
```
[Register] → pending → approved → (booking-eligible)
                     ↘ rejected
          approved → suspended → reinstated → approved
```
| Status | Meaning |
|--------|---------|
| `pending` | Just registered, awaiting light admin review |
| `approved` | Full access — can book trips |
| `rejected` | Declined — cannot access platform |
| `suspended` | Temporarily removed from platform |

> Approval here is **lightweight** (no background checks, no matching-eligibility gate). It exists mainly for trust & safety moderation, since participants will be sharing sensitive personal challenges.

### Specialist Status Flow (stored on `users.status` when `role = specialist`)
```
[Invited/Registered] → pending_verification → verified → active
                                             ↘ rejected
                        active → inactive (admin can pause without deleting)
```
| Status | Meaning |
|--------|---------|
| `pending_verification` | Credentials submitted, awaiting admin review |
| `verified` | Credentials confirmed |
| `active` | Eligible to be assigned to trips |
| `inactive` | Temporarily unavailable for assignment |
| `rejected` | Not approved to work on the platform |

### Booking Gate (applies to all users)
To book any trip, a user needs only:
1. Registered platform account
2. Verified email (`email_verified_at` is not null)
3. `users.status === 'approved'`
4. Stripe payment completed

> **There is no profile-completion gate and no matching-eligibility gate.** Booking is not conditioned on how complete the profile is — only on account verification/approval and payment. Challenge selection happens at booking time, not as a pre-requisite.

---

## 3. TRIP SCHEDULING MODEL

TheTrain runs on a published schedule rather than one-off ad hoc events. Two concepts:

### `trip_series` (the recurring definition, admin-configured)
| Column | Notes |
|--------|-------|
| `title`, `description` | |
| `cadence` | enum: `weekly`, `monthly` — admin sets per series |
| `day_of_week` | nullable int — used when `cadence = weekly` |
| `day_of_month` | nullable int — used when `cadence = monthly` |
| `default_capacity` | int |
| `default_base_price` | decimal |
| `default_accommodation_cost` | decimal — Airbnb cost baked into base price |
| `default_feeding_cost` | decimal — feeding cost baked into base price |
| `default_food_deduction_type` | enum: `flat`, `percentage` |
| `default_food_deduction_value` | decimal |
| `is_active` | boolean — admin can pause a whole series |

### `trips` (individual bookable instances, generated from a series or created standalone)
| Column | Notes |
|--------|-------|
| `trip_series_id` | nullable FK — null for one-off trips |
| `title`, `description` | |
| `venue`, `city` | |
| `start_date`, `end_date` | datetime — trips can span multiple days |
| `capacity` | int |
| `base_price` | decimal — **total inclusive price** (trip + accommodation + feeding) |
| `accommodation_cost` | decimal — informational breakdown component of `base_price` |
| `feeding_cost` | decimal — informational breakdown component of `base_price`; this is exactly what gets deducted/reduced if a user opts out of food |
| `food_deduction_type` | enum: `flat`, `percentage` — can override the series default per trip |
| `food_deduction_value` | decimal — flat currency amount OR percentage (0–100), per `food_deduction_type` |
| `status` | enum: `draft`, `open`, `closed`, `completed` |
| `cover_image` | string |

> **Pricing rule:** `feeding_cost` should generally equal or exceed the deduction actually applied — the deduction is a business decision (e.g. admin may only refund 70% of the feeding line item to cover fixed catering minimums), so `food_deduction_value` is intentionally independent from `feeding_cost`, not derived from it.

### `trip_challenges` (pivot — which challenge tracks this trip supports)
| Column | Notes |
|--------|-------|
| `trip_id`, `challenge_id` | FKs |
| Unique: `(trip_id, challenge_id)` | |

### `trip_specialists` (pivot — which specialist runs which challenge track on a given trip)
| Column | Notes |
|--------|-------|
| `trip_id`, `specialist_id`, `challenge_id` | FKs |
| `role_note` | nullable string, e.g. "Lead facilitator", "Co-facilitator" |
| Unique: `(trip_id, specialist_id, challenge_id)` | |

---

## 4. CHALLENGES SYSTEM

Challenges are the categories of personal difficulty a user selects when booking — this is the core domain concept of the platform.

### `challenges` table — **[DYNAMIC]**, admin-managed, never hardcoded
| Column | Notes |
|--------|-------|
| `name` | e.g. Divorce, Depression, Loneliness, Grief & Loss, Addiction Recovery, Anxiety, Burnout, Chronic Illness, Caregiver Fatigue |
| `slug` | |
| `description` | shown to users when selecting |
| `is_sensitive` | boolean — flags categories needing extra confidentiality handling in the UI (see Section 9) |
| `sort_order` | |
| `is_active` | boolean |

### `booking_challenges` (pivot — a user can select more than one challenge per booking)
| Column | Notes |
|--------|-------|
| `booking_id`, `challenge_id` | FKs |
| `is_primary` | boolean — one challenge marked as the user's primary focus, used to route them to the right specialist track/session |

> A user facing both divorce and loneliness selects both; `is_primary` decides which specialist track they're assigned to for session purposes, while the others are visible to admin/specialists as context.

---

## 5. SPECIALISTS SYSTEM

### `specialists` (profile table, `user_id` FK → `users` where `role = 'specialist'`)
| Column | Notes |
|--------|-------|
| `user_id` | FK |
| `display_name` | |
| `credentials` | string, e.g. "Licensed Clinical Psychologist" |
| `bio` | text |
| `photo_path` | |
| `years_experience` | int, nullable |
| `is_verified` | boolean — admin-toggled after credential review |
| `status` | see Section 2 |

### `specialist_challenges` (pivot — a specialist can cover multiple challenge categories)
| Column | Notes |
|--------|-------|
| `specialist_id`, `challenge_id` | FKs |

### Assignment flow
```
Admin creates/opens a trip
→ Admin selects which challenge tracks this trip will support (trip_challenges)
→ Admin assigns one or more verified, active specialists per challenge track (trip_specialists)
→ Users book the trip and select their challenge(s)
→ At the trip, each participant is grouped by their primary challenge with the assigned specialist
```

> There is no algorithmic matching here — assignment of specialists to trips/challenges is **entirely admin-driven**, and there is no participant-to-participant pairing at all.

---

## 6. PRICING & FOOD OPT-OUT MODEL

This is the most distinctive piece of TheTrain's domain logic.

### Components of a trip fee
```
base_price (all-inclusive)
   = trip_cost_component
   + accommodation_cost   (Airbnb)
   + feeding_cost
```

### Food opt-out flow
```
Booking screen → "Would you like feeding included?"
│
├── Yes (default) → pay full base_price
│
└── No → deduction applied per the trip's configured rule:
     ├── food_deduction_type = 'flat'       → final_price = base_price − food_deduction_value
     └── food_deduction_type = 'percentage' → final_price = base_price − (base_price × food_deduction_value / 100)
```

### `bookings` fee snapshot fields
At booking time, snapshot the trip's pricing config onto the booking row so historical bookings remain accurate even if admin later edits the trip:
| Column | Notes |
|--------|-------|
| `base_price_snapshot` | decimal |
| `opted_out_of_food` | boolean |
| `food_deduction_type_snapshot` | enum: `flat`, `percentage`, nullable |
| `food_deduction_value_snapshot` | decimal, nullable |
| `final_price` | decimal — computed at booking time using the logic above, this is what's actually charged via Stripe |

> `Booking::calculateFinalPrice()` encapsulates the deduction math above and must be the single source of truth — never recompute this ad hoc in a Blade/Livewire view.

---

## 7. DATABASE SCHEMA — CURRENT STATE (SUMMARY)

### `users`
| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `name` | string | |
| `email` | string | Unique, verified |
| `password` | hashed | |
| `role` | enum | `user`, `specialist`, `admin` |
| `status` | enum | See Section 2 |
| `phone` | string | nullable |
| `email_verified_at` | timestamp | nullable |
| `last_active_at` | timestamp | nullable |
| two_factor_* | — | Fortify 2FA columns |

### `profiles` — logistics/care-context only; no matching-oriented fields
| Column | Notes |
|--------|-------|
| `first_name`, `last_name` | |
| `date_of_birth` | date |
| `bio` | text, optional — short "what brings you here" note, private to admin/specialists only |
| `emergency_contact_name`, `emergency_contact_phone` | required before attending a trip |
| `dietary_restrictions` | JSON, **[DYNAMIC]** — relevant since feeding is bundled |
| `allergies` | text, nullable |
| `mobility_or_accessibility_needs` | text, nullable |
| `profile_visibility` | `private` by default — profiles here are never shown publicly or to other participants; only to admin and the specialist(s) assigned to that user's trip |

> **No `completion_percentage`, no `match_rate`, no `response_rate`, no compatibility fields.** This profile exists for logistics and care-context, not for matching.

### `trip_series`, `trips`, `challenges`, `trip_challenges`, `specialists`, `specialist_challenges`, `trip_specialists`
See Sections 3–5.

### `bookings`
| Column | Notes |
|--------|-------|
| `user_id`, `trip_id` | FKs |
| `status` | `pending_payment`, `confirmed`, `cancelled` |
| `base_price_snapshot`, `opted_out_of_food`, `food_deduction_type_snapshot`, `food_deduction_value_snapshot`, `final_price` | See Section 6 |
| `stripe_payment_intent_id`, `stripe_session_id`, `stripe_verified` | `stripe_verified` set only via signed Stripe webhook |
| `refund_issued`, `refunded_at` | admin-triggered only |
| `confirmed_at` | timestamp |

> **Not applicable to this platform:** no `booking_mode`, no `needs_matching`, no `paired_booking_id`/partner fields, no `memberships` table, no `matches` table, no `responses` (post-event yes/no) table.

### `booking_challenges`
See Section 4.

---

## 8. APPLICATION ROUTES

### Public (unauthenticated)
| Route | Component | Purpose |
|-------|-----------|---------|
| `/` | `Public\HomePage` | Marketing landing page |
| `/trips` | `Public\TripsList` | Browse upcoming trips (calendar/schedule view — weekly/monthly) |
| `/trips/{id}` | `Public\TripShow` | Single trip detail, incl. supported challenge tracks + specialists |

### Auth
| Route | Component |
|-------|-----------|
| `/login` | `Auth\Login` |
| `/register` | `Auth\Register` |
| `/verify-email` | `Auth\VerifyEmail` |

### User (`/user/*`, requires `auth` + `verified`)
| Route | Component | Purpose |
|-------|-----------|---------|
| `/user/dashboard` | `User\Dashboard` | Overview, upcoming trips |
| `/user/profile` | `User\ProfileBuilder` | Logistics/care-context profile form |
| `/user/trips` | `User\MyTrips` | User's booked trips |
| `/user/trips/{id}/book` | `User\TripBooking` | Select challenge(s), food opt-in/out, pay |
| `/user/trips/{id}` | `User\TripDetails` | Single booked-trip view |

### Specialist (`/specialist/*`, requires `auth` + `verified` + `role:specialist`)
| Route | Component | Purpose |
|-------|-----------|---------|
| `/specialist/dashboard` | `Specialist\Dashboard` | Upcoming trip assignments |
| `/specialist/trips/{id}` | `Specialist\TripRoster` | Roster of participants in their assigned challenge track for that trip |

### Admin (`/admin/*`, requires `auth` + `verified` + `role:admin`)
| Route | Component | Purpose |
|-------|-----------|---------|
| `/admin/` | `Admin\Dashboard` | KPI stats + recent activity |
| `/admin/users` | `Admin\UsersTable` | Paginated user list + filters |
| `/admin/users/{id}` | `Admin\UserReviewPanel` | User review — profile, status, booking history |
| `/admin/specialists` | `Admin\SpecialistsTable` | Manage specialists, verification status |
| `/admin/specialists/{id}` | `Admin\SpecialistReviewPanel` | Credential review, assign challenge coverage |
| `/admin/challenges` | `Admin\ChallengesManager` | CRUD for **[DYNAMIC]** challenge list |
| `/admin/trip-series` | `Admin\TripSeriesManager` | Create/edit recurring weekly/monthly series |
| `/admin/trips` | `Admin\TripsManager` | List/manage individual trips |
| `/admin/trips/create` | `Admin\TripEditor` | Create trip; set pricing, deduction rule, challenge tracks |
| `/admin/trips/{id}/edit` | `Admin\TripEditor` | Edit existing trip |
| `/admin/trips/{id}` | `Admin\TripShow` | Trip detail, roster by challenge track |
| `/admin/trips/{id}/specialists` | `Admin\TripSpecialistAssigner` | Assign specialists per challenge track |
| `/admin/trips/{id}/refunds` | `Admin\RefundManager` | Trigger Stripe refunds |
| `/admin/analytics` | `Admin\AnalyticsPanel` | Platform-wide stats (bookings, food opt-out rate, challenge distribution) |

> **Note:** `role:admin` middleware must be applied to all `/admin/*` routes, and `role:specialist` to all `/specialist/*` routes. Hard security requirement — see Section 11.

---

## 9. CONFIDENTIALITY & SENSITIVE-DATA HANDLING

Because users disclose personal struggles (divorce, depression, addiction, etc.), this platform carries higher sensitivity than a typical booking app:

- Challenge selections, the profile `bio`, and any specialist session notes are **never shown to other participants** and are **not public** under any `profile_visibility` setting — there is no `public` option at all.
- Only admin and the specific specialist(s) assigned to a user's challenge track on a specific trip can view that user's challenge selections and care-context notes.
- Challenges flagged `is_sensitive` (e.g. addiction recovery, grief) should get extra UI treatment (softer copy, optional visibility toggle for "primary challenge" shown to the group facilitator only, never to other participants) — but the flag drives UI/UX, not different DB access rules; access rules stay uniform and strict regardless of sensitivity flag.
- Specialist credential documents (uploaded during verification) must be stored on a private disk, never the public disk used for profile/trip photos.

---

## 10. ADMIN PANEL — COMPONENT STATUS

### To Build (Priority Order)
| Component | Purpose |
|-----------|---------|
| `Admin\ChallengesManager` | CRUD for dynamic challenge list |
| `Admin\TripSeriesManager` | Define weekly/monthly recurring series + defaults |
| `Admin\TripEditor` | Create/edit trips, set base price, accommodation/feeding breakdown, food deduction rule |
| `Admin\TripSpecialistAssigner` | Assign verified specialists to challenge tracks per trip |
| `Admin\SpecialistsTable` / `SpecialistReviewPanel` | Onboard and verify specialists |
| `Admin\UsersTable` / `UserReviewPanel` | Standard user management |
| `Admin\RefundManager` | Review + trigger Stripe refunds |
| `Admin\AnalyticsPanel` | Bookings, revenue, food opt-out rate, challenge-category distribution |
| `Admin\ContentManager` | Static pages, FAQs, email templates |

---

## 11. SECURITY NOTES

- **Admin routes protected by `role:admin` middleware; specialist routes by `role:specialist`.** Hard requirement, not optional.
- Stripe webhook endpoint must verify the Stripe signature before processing any payload — never trust frontend success redirects to mark payment as verified.
- `Booking::isPaymentVerified()` (`stripe_verified = true` AND `status = confirmed`) is the canonical payment check everywhere.
- Refunds are **admin-triggered only** — never expose a refund action to user-facing routes.
- A specialist can only view rosters/notes for trips they are explicitly assigned to via `trip_specialists` — never the full participant list of a trip, and never challenge tracks they aren't assigned to.
- Challenge selections and profile `bio`/care-context fields are sensitive personal data — treat with the same care as health information even though this is not a clinical record system.
- Specialist credential/verification documents stored on a private disk only.

---

## 12. WHAT TO ALWAYS KEEP DYNAMIC (NEVER HARDCODE)

1. ✅ Challenge category list (`challenges` table)
2. ✅ Dietary restriction options (dedicated options table, admin-editable)
3. ✅ Trip series cadence, defaults (capacity, pricing, deduction rule)
4. ✅ Per-trip pricing breakdown and food deduction type/value
5. ✅ Specialist-to-challenge coverage
6. ✅ Notification/email templates (to be built)

### Enums vs Dynamic Options
- **Use PHP/DB enums only for:** structural states — user status, specialist status, trip status, booking status, food_deduction_type (`flat`/`percentage`), trip cadence (`weekly`/`monthly`).
- **Use a dynamic options table for:** anything user-facing and admin-editable without a deploy — challenges, dietary restrictions, and any future preset lists.

---

## 13. KEY CONVENTIONS & PATTERNS

### Layouts
- `layouts.app`, `layouts.user`, `layouts.specialist`, `layouts.admin`, `layouts.public`, `layouts.auth`

### Livewire Component Naming
- `App\Livewire\Public\*`, `App\Livewire\Auth\*`, `App\Livewire\User\*`, `App\Livewire\Specialist\*`, `App\Livewire\Admin\*`
- Views mirror: `resources/views/livewire/{section}/{component}.blade.php`

### Stripe Integration Rules
- Always use Stripe webhooks to set `stripe_verified = true`.
- `final_price` (post food-deduction) is the amount actually sent to Stripe — never `base_price_snapshot`.
- Refunds triggered from the admin panel only, via Stripe's API.

---

## 14. IMMEDIATE PRIORITIES (Next Implementation Tasks)

1. **`role:admin` / `role:specialist` middleware** — protect `/admin/*` and `/specialist/*` routes
2. **`challenges` + `booking_challenges` migrations and seeder** — seed an initial dynamic challenge list (Divorce, Depression, Loneliness, Grief & Loss, Addiction Recovery, Anxiety, Burnout)
3. **`trip_series` + `trips` migrations** — including pricing breakdown and food deduction columns
4. **`Booking::calculateFinalPrice()`** — implement the flat/percentage deduction logic as the single source of truth
5. **Stripe webhook handler** — `payment_intent.succeeded` / `checkout.session.completed` → `stripe_verified = true`, `status = confirmed`
6. **`Admin\TripEditor`** — pricing breakdown fields + food deduction rule configuration
7. **`Admin\TripSpecialistAssigner`** — assign specialists to challenge tracks per trip
8. **`Specialist\TripRoster`** — scoped strictly to the specialist's assigned challenge track(s)
9. **`Admin\RefundManager`**
10. **`last_active_at` tracking** — update on auth events and meaningful user actions

---

*Last updated: 2026-07-31. This document must be updated whenever a new model, migration, route, or major feature is added.*
