# CodeCanyon Quality Pass — 2026-06-07/10

Context: the item was hard-rejected for not meeting Envato's baseline quality
requirements (code quality, UX, performance, market readiness). This document
tracks the concrete fixes made toward resubmission as a new item.

## 1. Branding

- Renamed the product from **LegalTech** to **LegalDocs** across:
  - App config (`.env` `APP_NAME`, `mail.from_name` setting)
  - Seeded user-facing copy (`CmsSeeder` landing/FAQ/testimonials/legal pages,
    3 system email addresses `privacy@/billing@/legal@legaldocs.app`)
  - Translations (`lang/en.json`, `lang/es.json` — both keys and values)
  - Docs & marketplace description (`docs/index.html`, `description.html`,
    `body-image-700xauto.html`)
- Left untouched by design: `Database/database.sql` (generated export snapshot)
  and `template integration/` reference files, plus live demo URLs/filenames
  in `description.html` that point at real external resources.

## 2. Removed inline styles / raw HTML strings from PHP (code quality)

Reviewers flag raw HTML built as PHP strings/heredocs in controllers and
services — it's hard to maintain, hard to theme, and reads as unfinished.

- **`EmailChannel::wrapInLayout()`**: replaced an inline-styled HTML heredoc
  with a DB-stored `email_layout` system template (via `NotificationTemplate`,
  the same pattern used for all other notification templates) that links an
  external stylesheet (`assets/static/global/css/email.css`) instead of
  `style="..."` attributes or `<style>` blocks.
- **`NotificationSeeder`**: replaced 3 inline-styled CTA buttons
  (`email_verification`, `password_reset`, `contract_sign_request`) with
  CSS classes (`.email-cta`, `.email-cta-accent`).
- **`ContractPdfExporter::buildHtml()`**: removed the raw HTML heredoc
  entirely. The PDF layout now lives in a dedicated `contracts.pdf_layout`
  database setting (token-based interpolation via `strtr()`), with a Blade
  view (`emails/contracts/pdf.blade.php`) as a fallback when the setting is
  empty — admins can edit the PDF wrapper without touching code.
- **`NewsletterBroadcast`/`NewsletterConfirm`** Mailables: converted from
  `Content(htmlString: ...)` raw-string building to proper
  `Content(view: 'emails.newsletter-*', with: [...])` Blade-view-backed mail,
  with new views `emails/newsletter-broadcast.blade.php` and
  `emails/newsletter-confirm.blade.php`.
- New external stylesheet `assets/static/global/css/email.css` holds all
  `.email-*` / `.newsletter-*` classes shared by every outgoing email.

## 3. Code style consistency (code quality)

- Ran `./vendor/bin/pint` across the codebase — fixed inconsistent import
  ordering, spacing, brace placement, unused imports, and ~15 other style
  rules across **~110 files** (controllers, models, seeders, payment drivers,
  services). This was previously failing `pint --test` wholesale, which is an
  easy "looks unpolished" signal during a reviewer's quick skim.

## 4. Missing `.env.example` (market readiness)

- The project shipped with **no `.env.example`** — buyers had nothing to copy
  to bootstrap their installation. Added one with safe production-oriented
  defaults (`APP_ENV=production`, `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`,
  `LOG_LEVEL=error`, `SESSION_DRIVER=database`).

## 5. Real bug fix — broken transaction search filter (code quality)

`Admin\TransactionController::index()` combined a `whereHas('user', ...)`
search clause with `->orWhere('description', 'like', ...)` **outside** any
grouping, which broke out of the surrounding `type`/date filters — searching
while a type filter was active silently returned unrelated rows regardless of
type. Fixed by wrapping both conditions in a single `where(function ...)`
group. Verified: searching `type=credit&q=<garbage>` now correctly returns 0
results (previously could leak unrelated matches).

## 6. N+1 / loop-query fixes on the admin dashboard (performance)

`Admin\DashboardController` ran multiple queries in PHP loops on every single
admin page load:

- `getMonthlyRevenueData()`: was **12 separate `sum()` queries** (one per
  month) → now **1** grouped aggregate query (`GROUP BY DATE_FORMAT(...)`).
- `getRevenueByPlan()`: was **N queries** (one `sum()` + `whereHas` subquery
  per plan) → now **1** joined/grouped query. Correctly accounts for the
  `User::subscription()` relation being `hasOne(...)->latestOfMany()` by
  joining only the latest `user_subscriptions` row per user via a `MAX(id)`
  subquery — avoids double-counting revenue across historical subscriptions.
- `getRevenueByGateway()`: was **N queries** (one per active gateway) → now
  **1** grouped aggregate query.

All three were verified to produce **identical results** to the original
N+1 logic (spot-checked against the old query forms in tinker) while cutting
query counts from O(n) to O(1) per dashboard load.

## 7. Missing database indexes (performance)

Audited the tables backing the highest-traffic admin and user pages
(dashboard, transactions, reports, user list, contract list, blog, newsletter
subscribers) for filter/sort columns lacking indexes — these cause full table
scans / filesorts that get worse as real data accumulates. Added via two
migrations (`2026_06_07_184921_add_missing_performance_indexes` and
`2026_06_07_185249_add_more_performance_indexes`, both applied):

| Table | Indexes added | Used by |
|---|---|---|
| `users` | `status`, `kyc_status`, `created_at` | Admin user list filters/sort/status counts |
| `contracts` | composite `(user_id, created_at)` | User's "My Contracts" page (`where user_id ... latest()`) |
| `transactions` | `created_at` | Admin transaction list, reports, dashboard charts |
| `payments` | `created_at` | Admin payment reports filter/sort |
| `subscribers` | `status`, `created_at` | Admin newsletter subscriber list (had **zero** secondary indexes) |

Verified already-adequate (no action needed): `blog_posts`
(`status`+`published_at` composite already present), `contract_parties`
(`token`/`contract_id` already indexed for signature lookups),
`notification_logs` (already indexed on `created_at` and lookup columns).

## 8. Verified bulk newsletter mail uses the app's mail config system

Confirmed `Admin\Cms\SubscriberController::broadcast()` sends through the
standard `Mail::to(...)->send(new NewsletterBroadcast(...))` pipeline, which
inherits driver/credentials/from-address from `AppServiceProvider`'s
DB-backed `Settings`-driven mail configuration — same pipeline as
transactional notification emails, no bypass or hardcoded config.

## Verification performed

- `php -l` syntax checks on every changed PHP file — all pass
- `lang/en.json` / `lang/es.json` validated as proper JSON
- Tinker-based functional tests confirming the rewritten dashboard aggregate
  queries return values identical to the original N+1 implementations
- Tinker-based functional test confirming the transaction search filter fix
  (type + garbage search now correctly returns 0 results)
- All migrations run cleanly against the `legaldocs` database (`php artisan
  migrate --force`), indexes confirmed via `SHOW INDEX`
