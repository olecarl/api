# AGENTS.md

Symfony 8 + API Platform 4 REST API. Local dev via DDEV (Docker).

## Commands

All commands run inside DDEV container. Prefix with `ddev` when running locally.

```bash
ddev composer build      # Full pipeline: prepare → lint → check → test
ddev composer prepare    # composer validate --strict + auto-fix code style (PHP CS Fixer)
ddev composer lint       # Lint Symfony container + Twig templates
ddev composer check      # Static analysis: PHP CS Fixer dry-run, PHPStan, Psalm
ddev composer test       # Validate Doctrine schema + Codeception tests
ddev composer report     # Codeception HTML report + PhpMetrics report in build/
```

## Console Commands

- `app:user:create` (`src/Command/CreateUserCommand.php`) - creates a user account.
  Interactive only (password is never passed as an argument). Options: `--role`
  (repeatable). Validates email and enforces a password of at least 12 chars.

## Authentication

JWT-based (LexikJWTAuthenticationBundle). No session cookies.

- Login: `POST /login_check` with JSON `{"email": ..., "password": ...}` → returns JWT token
- All API routes require `Authorization: Bearer <token>` (enforced via `access_control`)
- `login_check` is rate-limited to 5 attempts per minute (see `security.yaml`)
- Roles: `ROLE_ADMIN` and `ROLE_USER`; `User` resource operations are gated with
  `is_granted(...)` expressions in `src/ApiResource/User.php`
- Test bootstrap auto-generates ephemeral RSA keys in `var/test-jwt/`

Run single test suite:
```bash
ddev exec vendor/bin/codecept run Unit
ddev exec vendor/bin/codecept run Functional
ddev exec vendor/bin/codecept run Acceptance   # Requires DDEV running (hits https://api.ddev.site)
```

Run single test file:
```bash
ddev exec vendor/bin/codecept run Functional tests/Functional/SomeTestCest.php
```

## Static Analysis

- PHPStan: level max, analyzes `src/` only
- Psalm: errorLevel 1, finds unused code, ignores `src/DataFixtures/`
- PHP CS Fixer: Symfony rules + strict comparison, risky rules enabled

## Testing

Uses **Codeception** (not PHPUnit directly). Three suites:
- `Unit/` - isolated unit tests, organized by component
  (`Command/`, `Configuration/`, `Entity/`, `Repository/`, `State/`)
- `Functional/` - Symfony kernel + Doctrine, DB cleanup per test (`*Cest.php`)
- `Acceptance/` - PhpBrowser against live DDEV site (`https://api.ddev.site`)

Test database: SQLite at `data/database_test.sqlite`. CI creates this file manually before tests.
`tests/bootstrap.php` sets test env vars and generates ephemeral JWT keys.

Foundry is installed and its PHPUnit extension is enabled in `phpunit.dist.xml`,
but it is not currently used by the existing tests or fixtures.

## Project Structure

```
src/
  ApiResource/   # API Platform resource classes (DTOs, not entities)
  Command/       # Symfony console commands
  Entity/        # Doctrine entities (persistence only)
    Trait/       # Reusable entity traits (TimestampableTrait)
  DataFixtures/  # Fixture classes
  Repository/    # Doctrine repositories
  State/         # API Platform state providers/processors
```

## API Platform 4 Patterns

- Use `src/State/` for state providers/processors (not data providers)
- Keep DTOs in `src/ApiResource/`, entities in `src/Entity/`
- Entities use `TimestampableTrait` for automatic `createdAt`/`updatedAt`
- DTOs are `final readonly` classes with constructor promotion and a Uuid
  identifier marked with `#[ApiProperty(identifier: true)]`
- Wire the Doctrine entity via `stateOptions: new Options(entityClass: ...)`
  and a custom provider (see `src/ApiResource/User.php` + `src/State/UserProvider.php`)
- Gate operations with `security` expressions on the `#[ApiResource]` metadata

## Database

- Dev: PostgreSQL 17 via DDEV
- Test: SQLite (different from dev!)

Run migrations: `ddev console doctrine:migrations:migrate`

## CI

GitHub Actions runs on push to `master`/`develop` and PRs to `master`. The
workflow uses PHP 8.5, installs dependencies with `composer update`, creates the
SQLite test database, runs `composer check`, and executes
`vendor/bin/codecept run --fail-fast Unit Functional`.

The Acceptance suite targets `https://api.ddev.site` and requires DDEV to be
running. It is excluded from CI and must be run locally with DDEV.

## Quirks

- PHP CS Fixer excludes: `config/`, `var/`, `public/bundles`, `public/build`, `tests/Support/`, `vendor/`, `assets/`, `public/index.php`, `importmap.php`
- Codeception shuffles tests by default
- `--fail-fast` used in CI to stop on first failure
