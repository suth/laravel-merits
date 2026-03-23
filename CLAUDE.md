# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
composer test              # Run all tests
composer test-coverage     # Run tests with coverage
composer analyse           # Run PHPStan static analysis
composer format            # Run Pint code style fixer
```

Run a single test file:
```bash
vendor/bin/pest tests/Unit/BadgeContextTest.php
```

Run tests matching a name:
```bash
vendor/bin/pest --filter "can be created retroactively"
```

## Architecture

This is a Laravel package (`suth/laravel-merits`) that provides a self-contained user achievement badge system. The core design principle is that badges define their own criteria and triggers — host application business logic never needs to be modified.

### Key Classes

- **`Badge`** (`src/Badge.php`) — Base class all badge definitions extend. Badges implement contracts to declare how they are triggered and how they qualify recipients.
- **`BadgeContext`** (`src/BadgeContext.php`) — Readonly value object passed to `qualify()`. Holds a `Badgeable` recipient, an optional trigger (Eloquent model or event), and arbitrary `$meta`. Created via static factories: `retroactive()`, `fromModel()`, `fromEvent()`.
- **`Contracts/Badgeable`** — Interface that recipient models (e.g. `User`) must implement to be awarded badges.
- **`MeritsServiceProvider`** — Registers config (`config/merits.php`), migration stub, and the `laravel-merits` artisan command via `spatie/laravel-package-tools`.
- **`Facades/Merits`** — Facade pointing to `\Suth\Merits\Merits` (not yet implemented).

### Badge Contracts (referenced in README, not yet fully implemented)

Badges declare their behavior by implementing contracts:
- `EvaluatesEloquentEvents` — implement `eloquentListeners(): array` returning `[ModelClass => 'event']` and `resolveRecipient(object $trigger): ?Meritable`
- `EvaluatesRetroactively` — marks badge as evaluable in batch/retroactive runs
- `qualify(BadgeContext $context): bool` — core evaluation logic

### Testing Setup

Tests use Orchestra Testbench (`orchestra/testbench`) with an in-memory SQLite database. The `TestCase` runs all migrations from `database/migrations/` plus a `tests/database/migrations/CreateUsersTable.php` fixture.

Test fixtures live in `tests/Fixtures/`:
- `Models/User` — implements `Badgeable`, used as badge recipient
- `Models/Post` — plain Eloquent model used as trigger
- `Events/FakeWebhookEvent` — plain PHP object used as event trigger
- `Badges/SimpleBadge` — stub badge for testing (needs to be filled out as Badge logic is implemented)

### Config

`config/merits.php` exposes:
- `models.badge` — the Badge model class (defaults to `Suth\Merits\Models\Badge`)
- `table_names.badges` — table name (defaults to `badges`)
