# HTML Template Integration Guide

**For AI agents integrating a purchased HTML template into this Laravel SaaS starter kit.**

Read this entire file before touching a single file. Every section is load-bearing.

---

## Dual-repo workflow — IMPORTANT

**The rule:** `legaltech` is its own separate product. Only bugs found in **admin panel core components** (PHP-level: controllers, models, actions, services, middleware) that are clearly a base kit defect — not legaltech-specific — get mirrored to `starter-kit/`. Everything else stays in `legaltech/` only.

### When to update starter-kit

Only mirror a fix when **all three** of these are true:

1. The broken file lives under the admin panel (not frontend theme, not user views, not legaltech-specific features)
2. The bug is in PHP logic — a controller, model, action, service, or middleware — not in HTML/CSS/JS
3. The fix is generic (would benefit any product built on the starter kit, not just legaltech)

### Do NOT mirror to starter-kit

- Anything in `themes/default/` (layouts, partials, sections, pages, blog)
- Auth view shells, user view shells — HTML is legaltech-specific
- Legaltech-specific features, routes, migrations, or seeders
- Any template HTML/CSS work
- Frontend JS specific to legaltech

### Workflow when a mirrorable bug is found

1. Fix the file in `legaltech/src/`.
2. Confirm it meets all three criteria above.
3. Apply the identical fix to the corresponding file in `starter-kit/src/`.
4. Note it briefly in conversation so it's traceable.

---

## What this starter kit is

A Laravel 13 SaaS platform. The frontend theme lives entirely inside:

```
src/resources/views/themes/default/
```

The web root is the **repo root** (not `src/public/`). Built assets go to `/assets/`, never inside `src/`.

Tech stack: **Blade + Tailwind 4 + jQuery 3**. No Alpine, no Vue, no React, no Livewire. If the template uses Alpine or vanilla JS, keep it. If it uses a framework component library (Vue, React), rewrite those pieces in plain JS/jQuery.

---

## Step 0 — Drop the HTML files

Place all the template's raw `.html` files into:

```
template integration/html/
```

Do not edit them there. They are your reference source. Read them, then write Blade.

---

## Step 1 — Audit third-party assets

Go through every `<link>` and `<script>` in the template's HTML files.

**Rule:** If it is a pre-built vendor file (`.min.js`, `.min.css`, icon font, etc.), copy it to:

```
/assets/static/js/    ← JS libraries
/assets/static/css/   ← CSS libraries
```

Reference them in Blade via:
```blade
<link rel="stylesheet" href="{{ asset('assets/static/css/library.min.css') }}">
<script src="{{ asset('assets/static/js/library.min.js') }}"></script>
```

**Never** put hand-written files in `/assets/vite/` — that folder is wiped on every build.

If a library is already present (jQuery is at `/assets/static/js/jquery.min.js`, ApexCharts at `/assets/static/js/apexcharts.min.js`), do not add a second copy. jQuery is already loaded globally in every layout.

Load heavy page-specific libraries only on the pages that need them using `@push('head')` or `@push('scripts')`, not globally in the layout.

---

## Step 2 — Replace the layouts

There are three frontend layouts. Replace each one's visual shell with the template's equivalent, keeping all the Blade wiring intact.

### `src/resources/views/themes/default/layouts/frontend.blade.php`
Public marketing pages (home, about, blog, contact, pricing).

Required wiring to keep:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
@if($favicon = setting('site.favicon'))
    <link rel="icon" href="{{ $favicon }}" type="image/png">
@endif
<script src="{{ asset('assets/static/js/jquery.min.js') }}"></script>
<?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css', 'resources/js/app.js']); ?>
<x-integrations.ga4 />
@stack('head')

<x-flash />
@include('theme::partials.nav')
<main>{{ $slot ?? '' }}@yield('content')</main>
@include('theme::partials.footer')
<x-confirm-modal />
@stack('scripts')
<x-integrations.tawk />
@include('theme::partials.cookie-consent')
```

### `src/resources/views/themes/default/layouts/auth.blade.php`
Login, register, forgot password, reset password, 2FA pages.

Required wiring to keep:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('assets/static/js/jquery.min.js') }}"></script>
<?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css', 'resources/js/app.js']); ?>
@stack('head')
<x-flash />
{{-- Logo linking home --}}
<a href="{{ route('home') }}">{{ setting('site.name') }}</a>
{{-- Page heading slot --}}
<h1>{{ $heading ?? '' }}@yield('heading')</h1>
{{-- Page content slot --}}
{{ $slot ?? '' }}@yield('content')
<x-confirm-modal />
@stack('scripts')
```

### `src/resources/views/themes/default/layouts/user.blade.php`
Authenticated user dashboard, payments, payouts, wallet, profile, support.

Required wiring to keep:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('assets/static/js/jquery.min.js') }}"></script>
<?php echo \App\Support\ViteAssets::includeAssets(['resources/css/app.css', 'resources/js/app.js']); ?>
@stack('head')
<x-flash />
@include('theme::partials.nav')
@include('theme::partials.user-sidebar-compact')  {{-- sidebar --}}
{{ $slot ?? '' }}@yield('content')
@include('theme::partials.footer')
<x-confirm-modal />
@stack('scripts')
<x-integrations.tawk />
```

---

## Step 3 — Replace the partials

### Navigation — `partials/nav.blade.php`
Copy the template's nav HTML. Wire these dynamic parts:

```blade
{{-- Logo --}}
@if($logo = setting('site.logo'))
    <img src="{{ $logo }}" alt="{{ setting('site.name') }}" class="h-8">
@else
    <span class="font-bold text-brand-600">{{ setting('site.name') }}</span>
@endif

{{-- Guest vs authenticated links --}}
@guest('user')
    <a href="{{ route('login') }}">{{ __('Login') }}</a>
    <a href="{{ route('register') }}">{{ __('Sign up') }}</a>
@else
    <a href="{{ route('user.dashboard') }}">{{ __('Dashboard') }}</a>
    <form method="POST" action="{{ route('logout') }}">@csrf
        <button type="submit">{{ __('Logout') }}</button>
    </form>
@endguest
```

### Footer — `partials/footer.blade.php`
Copy template footer. Make these dynamic:
```blade
{{ setting('site.name') }}
{{ setting('site.tagline') }}
{{ now()->year }}   {{-- copyright year --}}
<a href="{{ route('page.show', 'privacy-policy') }}">Privacy Policy</a>
<a href="{{ route('page.show', 'terms') }}">Terms</a>
<a href="{{ route('page.show', 'cookie-policy') }}">Cookie Policy</a>
```

### User sidebar — `partials/user-sidebar-compact.blade.php`
Copy template's dashboard sidebar. Wire **all** links — missing any will cause a dead nav item:
```blade
{{-- User identity --}}
{{ auth('user')->user()->name }}
{{ auth('user')->user()->email }}

{{-- Navigation --}}
<a href="{{ route('user.dashboard') }}">Dashboard</a>
<a href="{{ route('user.wallet.index') }}">Wallet</a>
<a href="{{ route('user.payment.index') }}">Payments</a>
<a href="{{ route('user.payout.index') }}">Payouts</a>
<a href="{{ route('user.subscription.index') }}">Subscription</a>
<a href="{{ route('user.kyc.index') }}">Verification (KYC)</a>
<a href="{{ route('user.2fa.index') }}">Security (2FA)</a>
<a href="{{ route('user.notifications.index') }}">Notifications</a>
<a href="{{ route('user.support.index') }}">Support</a>
<a href="{{ route('user.profile') }}">Profile</a>
<form method="POST" action="{{ route('logout') }}">@csrf
    <button type="submit">Logout</button>
</form>
```

Highlight the active link by comparing the current route:
```blade
@if(request()->routeIs('user.dashboard'))
    {{-- active class --}}
@endif
```

---

## Step 4 — Auth screens

These are the functional screens. Each one already has working PHP logic — you are only replacing the HTML shell. **Do not touch form `action`, `method`, field `name` attributes, or `@csrf`.**

| File | Template page to match |
|------|------------------------|
| `src/resources/views/auth/login.blade.php` | Sign in page |
| `src/resources/views/auth/register.blade.php` | Sign up page |
| `src/resources/views/auth/forgot-password.blade.php` | Forgot password |
| `src/resources/views/auth/reset-password.blade.php` | Reset password |
| `src/resources/views/auth/two-factor-challenge.blade.php` | 2FA code entry |
| `src/resources/views/auth/verify-email.blade.php` | Email verification notice |

All extend `theme::layouts.auth`. Pattern:
```blade
@extends('theme::layouts.auth')
@section('heading', 'Sign in to your account')
@section('content')
    {{-- your template HTML here --}}
    {{-- keep all form fields, @csrf, @error() calls --}}
@endsection
```

Error display pattern (already in these files — keep it):
```blade
@error('email')
    <p class="text-sm text-red-600">{{ $message }}</p>
@enderror
```

Social login buttons (if template has them):
```blade
@if(setting('controls.google_login') === '1')
    <a href="{{ route('social.redirect', 'google') }}">Continue with Google</a>
@endif
```

---

## Step 5 — User dashboard screens

Each file extends `theme::layouts.user`. The data variables are already passed by the controller — do not change the controller. Only replace HTML.

### `src/resources/views/user/dashboard.blade.php`
Available variables:
```php
auth('user')->user()->name
auth('user')->user()->email
auth('user')->user()->subscription        // current subscription or null
$subscription->plan->name                 // plan name
$subscription->isActive()                 // bool
$recentPayments                           // Collection of Payment models
$walletBalance                            // formatted string e.g. "$120.00"
```

### `src/resources/views/user/payment/index.blade.php`
```php
$payments   // paginated Payment collection
// Each: $payment->amount, $payment->currency, $payment->status, $payment->created_at
// $payment->status values: 'pending', 'completed', 'failed', 'refunded'
```

### `src/resources/views/user/payment/create.blade.php`
Payment initiation form. Keep:
```blade
<form method="POST" action="{{ route('user.payment.store') }}">@csrf
    <input name="amount" ...>
    <input name="gateway" ...>  {{-- hidden or select --}}
</form>
```

### `src/resources/views/user/payment/pending.blade.php`
```php
$payment   // Payment model — $payment->amount, $payment->status, $payment->gateway
```
Show a "waiting for confirmation" state. No form needed.

### `src/resources/views/user/payment/success.blade.php`
```php
$payment   // Payment model — confirmed/completed
```
Show a success confirmation. Link back to dashboard: `route('user.dashboard')`.

### `src/resources/views/user/payout/index.blade.php`
```php
$payouts   // paginated Payout collection
// Each: $payout->amount, $payout->status, $payout->created_at, $payout->method
```

### `src/resources/views/user/payout/create.blade.php`
Requires KYC approval — the route is already guarded by `middleware('kyc')`, no extra check needed in the view.
```blade
<form method="POST" action="{{ route('user.payout.store') }}">@csrf
    <input name="amount" ...>
    <input name="method" ...>
    <input name="account_details" ...>
</form>
```

### `src/resources/views/user/payout/pending.blade.php`
```php
$payout   // Payout model — $payout->amount, $payout->status, $payout->method
```
Show a "payout under review" state. Cancel button:
```blade
<form method="POST" action="{{ route('user.payout.cancel', $payout) }}">@csrf
    <button type="submit">Cancel request</button>
</form>
```

### `src/resources/views/user/wallet/index.blade.php`
```php
$balance        // Money value object — use {{ $balance }} to render
$transactions   // paginated WalletTransaction collection
```

### `src/resources/views/user/profile.blade.php`
Route: `GET /user/profile` → `route('user.profile')`
Two separate forms on the same page:
```blade
{{-- Profile details form --}}
<form method="POST" action="{{ route('user.profile.update') }}">@csrf @method('PUT')
    <input name="name" value="{{ auth('user')->user()->name }}">
    <input name="email" value="{{ auth('user')->user()->email }}">
</form>

{{-- Password change form --}}
<form method="POST" action="{{ route('user.profile.password') }}">@csrf @method('PUT')
    <input name="current_password" type="password">
    <input name="password" type="password">
    <input name="password_confirmation" type="password">
</form>
```

### `src/resources/views/user/subscription/index.blade.php`
Shows the user's current subscription status.
```php
$subscription   // current Subscription model or null
// $subscription->plan->name
// $subscription->status  — values: 'active', 'trial', 'past_due', 'canceled', 'expired'
// $subscription->isActive()       // bool
// $subscription->ends_at          // Carbon or null
// $subscription->renews_at        // Carbon or null
```
Link to upgrade: `route('user.subscription.plans')`

### `src/resources/views/user/subscription/plans.blade.php`
Plan selection / upgrade page.
```php
$plans          // Collection of active Plan models
$subscription   // current subscription or null
// $plan->name, $plan->price, $plan->interval ('month'/'year'), $plan->features (array)
```
Subscribe form:
```blade
<form method="POST" action="{{ route('user.subscription.subscribe') }}">@csrf
    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
    <button type="submit">Subscribe</button>
</form>
```

### `src/resources/views/user/kyc/index.blade.php`
KYC status page. Has **four states** — all must be covered:
```blade
@if($user->kyc_status === 'approved')
    {{-- verified badge --}}
@elseif($user->kyc_status === 'pending' && $submission)
    {{-- under review — show submission date: $submission->created_at --}}
@elseif($user->kyc_status === 'rejected' && $submission)
    {{-- rejected — show reason: $submission->rejection_reason --}}
    {{-- link to re-submit: route('user.kyc.create') --}}
@else
    {{-- not submitted — link to start: route('user.kyc.create') --}}
@endif
```

### `src/resources/views/user/kyc/create.blade.php`
Document submission form.
```blade
<form method="POST" action="{{ route('user.kyc.store') }}" enctype="multipart/form-data">@csrf
    <input type="text" name="full_name" ...>
    <select name="id_type">
        <option value="passport">Passport</option>
        <option value="national_id">National ID</option>
        <option value="drivers_license">Driver's License</option>
    </select>
    <input type="text" name="id_number" ...>
    <input type="file" name="document_front" ...>
    <input type="file" name="document_back" ...>   {{-- optional --}}
    <input type="file" name="selfie" ...>
</form>
```

### `src/resources/views/user/2fa/index.blade.php`
2FA status page. Variable: `$user->two_factor_enabled` (bool).
Links: enable → `route('user.2fa.setup')`, disable form:
```blade
<form method="POST" action="{{ route('user.2fa.disable') }}">@csrf
    <button type="submit">Disable 2FA</button>
</form>
```

### `src/resources/views/user/2fa/setup.blade.php`
QR code setup page.
```php
$qrCodeSvg   // SVG string — output with: <?php echo $qrCodeSvg; ?>
$secret      // manual entry key string
```
Confirm form:
```blade
<form method="POST" action="{{ route('user.2fa.enable') }}">@csrf
    <input type="text" name="code" inputmode="numeric" ...>
    <button type="submit">Enable</button>
</form>
```

### `src/resources/views/user/2fa/recovery-codes.blade.php`
```php
$recoveryCodes   // array of strings
```
Regenerate form:
```blade
<form method="POST" action="{{ route('user.2fa.regenerate') }}">@csrf
    <button type="submit">Regenerate codes</button>
</form>
```

### `src/resources/views/user/notifications/index.blade.php`
```php
$notifications   // paginated notification collection
// Each: $n->data['message'], $n->data['title'], $n->read_at (null = unread), $n->created_at
```
Mark read:
```blade
<form method="POST" action="{{ route('user.notifications.read', $n->id) }}">@csrf
    <button type="submit">Mark read</button>
</form>
{{-- Mark all --}}
<form method="POST" action="{{ route('user.notifications.read-all') }}">@csrf
    <button type="submit">Mark all read</button>
</form>
```

### `src/resources/views/user/support/index.blade.php` and `create.blade.php`
```php
$tickets   // paginated SupportTicket collection (index)
// $ticket->reference, $ticket->subject, $ticket->status, $ticket->created_at
```
New ticket form (`create.blade.php`):
```blade
<form method="POST" action="{{ route('user.support.store') }}">@csrf
    <input name="subject" ...>
    <textarea name="message" ...></textarea>
</form>
```

### `src/resources/views/user/support/show.blade.php`
```php
$ticket            // SupportTicket model
$ticket->messages  // reply collection — each: $m->body, $m->created_at, $m->is_staff
```
Reply and close forms:
```blade
<form method="POST" action="{{ route('user.support.reply', $ticket->reference) }}">@csrf
    <textarea name="message" ...></textarea>
    <button type="submit">Send reply</button>
</form>
<form method="POST" action="{{ route('user.support.close', $ticket->reference) }}">@csrf
    <button type="submit">Close ticket</button>
</form>
```

---

## Step 6 — CMS page sections

CMS pages are built from sections. Each section type has a view at:
```
src/resources/views/themes/default/sections/{type}.blade.php
```

Existing types: `hero`, `features`, `cta`, `cta_strip`, `stats`, `gallery`, `logos`, `content`, `latest_blog_posts`, `custom_html`.

For each section, replace the stub HTML with the template's matching block. The data pattern is always:

```blade
@php $d = $section->getLocaleData(); @endphp
{{-- $d is an array of the section's editable fields --}}
<h2>{{ $d['heading'] ?? '' }}</h2>
<p>{{ $d['subheading'] ?? '' }}</p>
```

To add a **new** section type (e.g. `testimonials`):
1. Create `src/resources/views/themes/default/sections/testimonials.blade.php`
2. Add `'testimonials'` to the type enum in `src/app/Domain/Cms/Enums/SectionType.php` if it exists, otherwise add it to the seeder and admin CMS section-type select.

---

## Step 7 — Marketing pages

### Contact — `src/resources/views/themes/default/pages/contact.blade.php`
Keep the form wired:
```blade
<form method="POST" action="{{ route('contact.submit') }}">@csrf
    <input name="name" ...>
    <input name="email" ...>
    <textarea name="message" ...></textarea>
</form>
```

### Plans/Pricing — `src/resources/views/themes/default/pages/plans/`
```php
$plans   // Collection of Plan models
// $plan->name, $plan->price, $plan->interval ('month'/'year'), $plan->features
```
Subscribe button:
```blade
<a href="{{ route('user.subscription.checkout', $plan) }}">Get started</a>
```

### Blog — `src/resources/views/themes/default/blog/`
```php
// index: $posts (paginated), $categories, $tags
// show:  $post->title, $post->body (rendered HTML), $post->author, $post->published_at
// Output post body with: <?php echo $post->body; ?>
// (never use {!! !!} — project rule, use raw PHP echo instead)
```

---

## Step 8 — Dynamic text rules

| What | How |
|------|-----|
| Site name | `setting('site.name')` |
| Site tagline | `setting('site.tagline')` |
| Site email | `setting('site.email')` |
| Site phone | `setting('site.phone')` |
| Logo URL | `setting('site.logo')` |
| Favicon URL | `setting('site.favicon')` |
| Currency symbol | `setting('site.currency_symbol')` |
| Any hardcoded string | Wrap in `{{ __('Your string') }}` |
| Raw HTML (blog body, custom HTML section) | `<?php echo $variable; ?>` — never `{!! !!}` |
| Formatted money amounts | `formatAmount($amount)` |

---

## Step 9 — Built-in integrations (must be compatible)

These features are already implemented in the starter kit. The template must not break them. Each one has a specific integration point the agent must wire into the replaced view.

### Cloudflare Turnstile (CAPTCHA)

Turnstile is enabled/disabled via Admin → Settings → Integrations. When enabled it automatically renders on login, register, and contact forms.

The component is a single Blade tag — place it inside the `<form>` just before the submit button:
```blade
<x-turnstile />
```

It self-guards (`@if Turnstile::enabled()`) so it renders nothing when disabled. Do not add any other captcha. Do not remove this tag from login, register, or contact forms.

Checklist: `<x-turnstile />` is present in login, register, and contact forms.

---

### Maintenance Mode

Controlled via Admin → Settings → Controls → Maintenance Mode toggle. When on, all frontend routes return the `errors/503` view. Admin panel stays accessible.

The agent must replace `src/resources/views/errors/503.blade.php` with the template's maintenance/503 page. Requirements:
- Standalone HTML — no layout extension, no nav, no footer
- **The current file uses `@vite(...)` which is wrong for this project — replace it entirely**
- Load CSS via `asset('assets/static/css/...')` only — do not use `ViteAssets::includeAssets()` here, the app cache may be unavailable during maintenance
- Show site name: `{{ config('app.name') }}`
- No jQuery dependency — page must render with zero JS

Do not modify `CheckMaintenance.php` — the middleware logic is complete.

---

### SMS / Phone Verification

Controlled via Admin → Settings → Controls → SMS Verification toggle. When enabled, users must verify their phone number after login before accessing the dashboard.

The agent must replace `src/resources/views/auth/phone-verify.blade.php` with the template's phone verification screen. This view has **two states** driven by `$hasPhone`:

```blade
@if($hasPhone)
    {{-- State 1: user has a phone on file — show code entry form --}}
    {{-- form action="{{ route('phone.verify') }}" --}}
    {{-- field name="code" --}}
    {{-- resend form: action="{{ route('phone.send') }}", hidden fields mobile_country_code + mobile --}}
@else
    {{-- State 2: no phone yet — show phone number entry form --}}
    {{-- form action="{{ route('phone.send') }}" --}}
    {{-- fields: name="mobile_country_code", name="mobile" --}}
@endif
{{-- Always include logout form at bottom --}}
<form method="POST" action="{{ route('logout') }}">@csrf ...</form>
```

Do not change field names, route names, or the `$hasPhone` condition.

---

### Custom CSS

Admins can inject arbitrary CSS via Admin → Settings → Brand & Appearance → Custom CSS. The CSS is saved to `/assets/static/css/custom.css` and must be loaded in the frontend layout inside `<head>`, after the Vite assets:

```blade
<link rel="stylesheet" href="{{ asset('assets/static/css/custom.css') }}?v={{ filemtime(public_path('assets/static/css/custom.css')) }}">
```

This line is already present in `themes/default/layouts/frontend.blade.php`. **Do not remove it when replacing the layout.** The `?v=...` cache-buster ensures changes take effect immediately without browser caching issues.

Do not load this file in the `auth` or `user` layouts — custom CSS is frontend-only.

---

### Third-party Integrations (GA4, Tawk.to)

These are injected automatically via Blade components already present in the layouts:

```blade
<x-integrations.ga4 />    {{-- in <head> --}}
<x-integrations.tawk />   {{-- before </body> --}}
```

Both are self-guarded — they render nothing when disabled in settings. **Do not remove these tags when replacing layouts.** Do not add any other analytics or live-chat script manually — configure them through Admin → Settings → Integrations.

---

## Step 10 — JS conventions

jQuery (`$`) is available globally — no import needed. Use it freely in inline `<script>` blocks.

For confirmations:
```js
window.confirmModal.show('Title', 'Are you sure?', function() {
    // confirmed
});
```

For toast notifications:
```js
window.toast('Saved successfully', 'success');  // types: success, error, warning, info
```

Never use native `confirm()` or `alert()`.

Never use shorthand event methods `.click()`, `.change()`, `.hover()`, `.blur()`, `.focus()`, `.keyup()`, etc. Always use `.on()`:
```js
// Wrong
$('#btn').click(function () { ... });
$('select').change(function () { ... });

// Correct
$('#btn').on('click', function () { ... });
$('select').on('change', function () { ... });
```

Every auto-executing block must open with `'use strict';`:
```js
$(function () {
    'use strict';
    // your code
});

document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    // your code
});
```

---

## Step 10 — What NOT to do

- Do not create a `src/public/` folder. Assets do not go there.
- Do not edit files in `/assets/vite/` — they are auto-generated and will be overwritten.
- Do not use `{!! $var !!}` anywhere. Use `<?php echo $var; ?>` for raw output.
- Do not add Alpine.js, Vue, React, or Livewire — the stack is Blade + jQuery only.
- Do not import jQuery in JS files — it is already on `window.$`.
- Do not change form `action`, `method`, field `name` attributes, `@csrf`, or `@error()` calls in auth/user screens.
- Do not remove `<x-flash />`, `<x-confirm-modal />`, or the ViteAssets include from layouts.
- Do not load a library globally in the layout if it is only needed on one page — use `@push('head')` / `@push('scripts')`.
- Do not add a JS or CSS package to `/assets/static/` if it is already there. Check the folder before copying — duplicates with different filenames (e.g. `jquery.min.js` and `jquery-3.7.js`) are not allowed.
- Do not leave commented-out code in any file. If code is not needed, delete it.
- Do not use raw inline `style="..."` attributes. Use Tailwind utility classes instead. If a style has no Tailwind equivalent, add it to the page's `@push('head')` block inside a `<style>` tag.

---

## File map summary

```
Template HTML file          →  Blade file to edit
─────────────────────────────────────────────────────────────────────────────
index.html (homepage)       →  themes/default/sections/hero.blade.php
                               themes/default/sections/features.blade.php
                               themes/default/sections/cta.blade.php  (etc.)
login.html                  →  auth/login.blade.php
register.html               →  auth/register.blade.php
forgot-password.html        →  auth/forgot-password.blade.php
reset-password.html         →  auth/reset-password.blade.php
2fa-challenge.html          →  auth/two-factor-challenge.blade.php
verify-email.html           →  auth/verify-email.blade.php
phone-verify.html           →  auth/phone-verify.blade.php          ← two states
maintenance.html / 503.html →  errors/503.blade.php                 ← standalone only
dashboard.html              →  user/dashboard.blade.php
payments.html               →  user/payment/index.blade.php
payment-create.html         →  user/payment/create.blade.php
payment-pending.html        →  user/payment/pending.blade.php
payment-success.html        →  user/payment/success.blade.php
payouts.html                →  user/payout/index.blade.php
payout-create.html          →  user/payout/create.blade.php
payout-pending.html         →  user/payout/pending.blade.php
wallet.html                 →  user/wallet/index.blade.php
subscription.html           →  user/subscription/index.blade.php
plans-upgrade.html          →  user/subscription/plans.blade.php
kyc.html                    →  user/kyc/index.blade.php             ← four states
kyc-submit.html             →  user/kyc/create.blade.php
2fa-settings.html           →  user/2fa/index.blade.php
2fa-setup.html              →  user/2fa/setup.blade.php
2fa-recovery.html           →  user/2fa/recovery-codes.blade.php
notifications.html          →  user/notifications/index.blade.php
support.html                →  user/support/index.blade.php
support-create.html         →  user/support/create.blade.php
support-ticket.html         →  user/support/show.blade.php
profile.html                →  user/profile.blade.php
plans.html (public)         →  themes/default/pages/plans/index.blade.php
blog-list.html              →  themes/default/blog/index.blade.php
blog-single.html            →  themes/default/blog/show.blade.php
contact.html                →  themes/default/pages/contact.blade.php
about.html / static pages   →  CMS pages — edit via Admin → CMS → Pages
nav (header)                →  themes/default/partials/nav.blade.php
footer                      →  themes/default/partials/footer.blade.php
sidebar                     →  themes/default/partials/user-sidebar-compact.blade.php
```

---

## Checklist before marking integration complete

- [ ] All layouts have csrf-token meta, ViteAssets include, jQuery script tag, flash component, confirm-modal component
- [ ] Nav shows correct guest/auth state
- [ ] Login, register, forgot-password, reset-password forms submit correctly (no field name changes)
- [ ] User dashboard loads with real subscription and payment data
- [ ] Payment create → pending → success flow works end to end
- [ ] Payout create → pending (with cancel button) → index flow works
- [ ] Wallet balance and transaction list display correctly
- [ ] Subscription index shows current plan status; plans page lists active plans with subscribe form
- [ ] KYC index renders all four states (approved / pending / rejected / not submitted); create form submits documents
- [ ] 2FA index shows enabled/disabled state; setup page renders QR code; recovery codes page lists codes
- [ ] Notifications index lists notifications with mark-read and mark-all-read
- [ ] Support index, create, and show (with reply + close) all work
- [ ] Profile form saves name/email; password form saves password
- [ ] Public plans page lists active plans with correct prices
- [ ] Blog index and post pages render
- [ ] Contact form submits without error
- [ ] CMS pages (`/about`, `/privacy-policy`, `/terms`, `/cookie-policy`, `/refund-policy`) resolve correctly
- [ ] Cookie consent banner appears on first visit when enabled in settings
- [ ] No hardcoded site name, logo, or currency — all pulled from `setting()`
- [ ] No `{!! !!}` syntax anywhere in Blade files
- [ ] All third-party vendor files are in `/assets/static/` not `/assets/vite/`
- [ ] No page-specific heavy libraries loaded in the global layout
- [ ] No shorthand jQuery event methods — `.click()`, `.change()`, `.hover()`, etc. — all replaced with `.on('event', ...)`
- [ ] No commented-out code anywhere — in Blade, JS, or CSS. Dead code must be deleted, not commented.
- [ ] No raw inline `style="..."` attributes — replaced with Tailwind classes or a scoped `<style>` block in `@push('head')`
- [ ] No duplicate packages in `/assets/static/` — verify each library isn't already present before copying
- [ ] `<x-turnstile />` present inside login, register, and contact forms
- [ ] `errors/503.blade.php` replaced with template's maintenance page — standalone, no layout extension
- [ ] `auth/phone-verify.blade.php` replaced with template's phone verification screen — both `$hasPhone` states covered, field names unchanged
- [ ] `<x-integrations.ga4 />` and `<x-integrations.tawk />` retained in all layouts — not removed or duplicated
- [ ] Custom CSS `<link rel="stylesheet" href="{{ asset('assets/static/css/custom.css') }}?v=...">` retained in `frontend.blade.php` — not removed or moved to auth/user layouts
- [ ] Every `$(document).ready()`, `$(function(){})`, `document.addEventListener('DOMContentLoaded', ...)`, and any other auto-executing function block has `'use strict';` as its first statement
