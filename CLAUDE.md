# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Symfony 8.0 + API Platform 4.2 REST API. PHP 8.5, PostgreSQL 17, Doctrine ORM 3.6, DDEV for local dev.

## Dev setup

```bash
composer install
ddev start
ddev launch   # open in browser
```

## Common commands

```bash
# Full pipeline (prepare → lint → check → test)
ddev composer run build

# Format code (PHP CS Fixer)
ddev composer run prepare

# Lint Symfony container + Twig
ddev composer run lint

# Static analysis only (CS check + PHPStan + Psalm)
ddev composer run check

# Tests only (Doctrine schema validation + Codeception)
ddev composer run test

# Generate HTML test report + PHP Metrics
ddev composer run report

# Database
ddev console doctrine:migrations:migrate
ddev adminer   # database UI

# Run a single Codeception suite or test
vendor/bin/codecept run Rest
vendor/bin/codecept run Rest Api/TodoCest.php
```

## Code style

- **PHP CS Fixer** with `@Symfony` + `@PHP85Migration` + risky rules. Run via `composer run prepare` (auto-fix) or `composer run check` (dry-run).
- **PHPStan** at level 10 + **Psalm** on `src/` and `config/`.
- Strict comparisons, strict params, `mb_str_functions` enforced.
- All files must have `declare(strict_types=1)`.

## Testing

Four Codeception suites — run in this order by CI:

| Suite | What it tests | DB |
|---|---|---|
| `Unit` | Pure logic, no framework | — |
| `Functional` | Symfony container + Doctrine | SQLite (auto-created) |
| `Rest` | API endpoints via HTTP | SQLite |
| `Acceptance` | Browser (PhpBrowser) | — |

Test DB is SQLite at `data/database_test.sqlite` (see `.env.test`). Production uses PostgreSQL.

## Architecture

### API Platform resources

API resources live in `src/ApiResource/` using PHP 8 attributes (`#[ApiResource]`). Resources use:
- Separate **input DTOs** (e.g. `CreateTodo`) for write operations
- Separate **output DTOs** (e.g. `CollectionTodo`) for read operations
- `#[Map]` attribute to map between entity and DTO
- `stateOptions` pointing to the backing Doctrine entity

### Entities

Entities in `src/Entity/` use Doctrine attribute mapping. Conventions:
- UUID v7 primary keys (`#[ORM\GeneratedValue(strategy: 'CUSTOM')]`)
- `TimestampableEntity` trait from Gedmo extensions for `createdAt`/`updatedAt`
- Underscore naming strategy (snake_case in DB)

### State providers/processors

Custom business logic goes in `src/State/Provider/` and `src/State/Processor/`. Most simple CRUD operations use API Platform's built-in entity state handlers without custom providers.

### Security

- Users load by email field; hashed passwords via bcrypt
- Email verification via SymfonyCasts bundle
- Roles: `ROLE_USER`, `ROLE_ADMIN`
- Test environment uses low-cost bcrypt for performance

### CORS

Configured in `config/packages/nelmio_cors.yaml`. Allows GET, OPTIONS, POST, PUT, PATCH, DELETE. Exposes the `Link` header (used by API Platform for pagination).

## CI

GitHub Actions (`.github/workflows/symfony.yml`) runs on push to `develop` and PRs to `master`:
1. PHP 8.4 setup
2. `composer install`
3. Create SQLite test DB
4. `vendor/bin/codecept run`
