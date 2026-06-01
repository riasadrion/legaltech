# Shared Modules

The starter-kit is the **single source of truth** for a set of generic,
project-agnostic modules. Every project built from this kit ships a copy of
them. They are **not** a Composer package — they are copied at scaffold time
and kept in sync manually, verified with `scripts/check-shared-drift.sh`.

## Rules

1. **Edit shared modules in the starter-kit first**, then copy the change into
   each project. Never make a generic fix directly in a project — it will drift.
2. **Never put project-specific logic in a shared module.** Credentials, the
   Contract domain, branding, and any business logic unique to one product stay
   out. If a shared module needs a project hook, add an extension point
   (interface, event, config) rather than project code.
3. Shared modules must not reference project-specific domains
   (Contract, Payment, Subscription, Support, Wallet, etc.). This is enforced by
   the drift check.

## Shared domains (`app/Domain/`)

- `Admin`        — admin navigation / shell
- `Auth`         — authentication, social login, 2FA, phone verification
- `Blog`         — blog posts, categories, tags
- `Cms`          — pages, sections, menus, newsletter
- `Forms`        — custom form builder
- `I18n`         — locales, translations, auto-translation
- `Notifications`— DB-driven notification templates + channels
- `Ops`          — operational models / reports
- `Settings`     — key/value settings store
- `Upload`       — file upload + storage abstraction (local / S3-compatible)

## Shared models (`app/Models/`)

ActivityLog, Admin, AdminNotification, Permission, Role, Setting,
SocialAccount, UserLogin, UserNotification.

> `User.php` is **shared as a starting point** but is expected to diverge once a
> project adds its own relations (e.g. contracts, wallet). It is therefore
> excluded from the strict drift check.

## Project-specific domains (NOT shared)

Contract, Payment, Subscription, Support, Wallet — these live only in the
project that needs them.

## Checking drift

```bash
# From a project root, compare its shared modules against the starter-kit:
scripts/check-shared-drift.sh /path/to/starter-kit/src
```

Exit code 0 = in sync. Non-zero = drift found (diff printed).
