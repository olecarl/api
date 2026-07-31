# AGENTS.md

Symfony 8 + API Platform 4 REST API. Local dev via DDEV (Docker).

## Commands

All commands run inside DDEV container. Prefix with `ddev` when running locally.

```bash
ddev composer run build      # Full pipeline: prepare → lint → check → test
ddev composer run prepare    # Auto-fix code style (PHP CS Fixer)
ddev composer run lint       # Lint Symfony container + Twig templates
ddev composer run check      # Static analysis: PHP CS Fixer dry-run, PHPStan, Psalm
ddev composer run test       # Validate Doctrine schema + Codeception tests
```

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
- `Unit/` - isolated unit tests
- `Functional/` - Symfony kernel + Doctrine, DB cleanup per test
- `Acceptance/` - PhpBrowser against live DDEV site

Test database: SQLite at `data/database_test.sqlite`. CI creates this file manually before tests.

Foundry used for fixtures (PHPUnit extension enabled in `phpunit.dist.xml`).

## Project Structure

```
src/
  ApiResource/   # API Platform resource classes (DTOs, not entities)
  Controller/    # Symfony controllers
  Entity/        # Doctrine entities (persistence only)
    Trait/       # Reusable entity traits (TimestampableTrait)
  Repository/    # Doctrine repositories
  Service/       # Business logic services
  State/         # API Platform state providers/processors
  Security/      # Voters, authenticators
  DataFixtures/  # Fixture classes (excluded from Psalm)
```

## API Platform 4 Patterns

- Use `src/State/` for state providers/processors (not data providers)
- Keep DTOs in `src/ApiResource/`, entities in `src/Entity/`
- Entities use `TimestampableTrait` for automatic `createdAt`/`updatedAt`

## Database

- Dev: PostgreSQL 17 via DDEV
- Test: SQLite (different from dev!)

Run migrations: `ddev console doctrine:migrations:migrate`

## CI

GitHub Actions runs on push to `master`/`develop` and PRs to `master`. Pipeline:
1. `composer check` (static analysis)
2. `vendor/bin/codecept run --fail-fast`

## Quirks

- PHP CS Fixer excludes: `config/`, `var/`, `public/bundles`, `public/build`, `tests/Support/`, `vendor/`, `assets/`, `public/index.php`, `importmap.php`
- Codeception shuffles tests by default
- `--fail-fast` used in CI to stop on first failure
