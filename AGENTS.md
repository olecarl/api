# AGENTS.md

Symfony 8 + API Platform 4 REST API. Local dev via DDEV (Docker).

## Commands

All commands run inside DDEV container. Prefix with `ddev` when running locally.

```bash
ddev composer build      # Full pipeline: prepare → lint → check → test
ddev composer prepare    # Auto-fix code style (PHP CS Fixer)
ddev composer lint       # Lint Symfony container + Twig templates
ddev composer check      # Static analysis: PHP CS Fixer dry-run, PHPStan, Psalm
ddev composer test       # Validate Doctrine schema + Codeception tests
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

## Database

- Dev: PostgreSQL 17 via DDEV
- Test: SQLite (different from dev!)

Run migrations: `ddev console doctrine:migrations:migrate`

## CI

GitHub Actions runs on push to `master`/`develop` and PRs to `master`. The
workflow uses PHP 8.5, installs dependencies with `composer update`, creates the
SQLite test database, runs `composer check`, builds Codeception, and executes
`vendor/bin/codecept run --fail-fast`.

The final command includes the Acceptance suite, which targets
`https://api.ddev.site`. The workflow does not start DDEV, so this CI setup must
be adjusted before Acceptance tests can run reliably on GitHub-hosted runners.

## Quirks

- PHP CS Fixer excludes: `config/`, `var/`, `public/bundles`, `public/build`, `tests/Support/`, `vendor/`, `assets/`, `public/index.php`, `importmap.php`
- Codeception shuffles tests by default
- `--fail-fast` used in CI to stop on first failure
