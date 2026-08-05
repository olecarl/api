# RESTful Web API Framework

[![Symfony](https://github.com/olecarl/api/actions/workflows/symfony.yml/badge.svg?branch=master)](https://github.com/olecarl/api/actions/workflows/symfony.yml)

REST API using API Platform distribution of Symfony PHP Framework.
Local development environment managed by DDEV using Docker containers.

## Features

- Content Negotiation (JSON-LD, HAL, JSON:API, JSON, XML, YAML)
- JWT Authentication
- OpenAPI 3.0 Documentation
- Continuous Integration
- Automated Testing with Code Coverage
- CORS Support

## Stack

| Category | Technology |
|----------|------------|
| Language | PHP 8.5 |
| Framework | Symfony 8.0, API Platform 4.3 |
| Database | PostgreSQL 17 (dev), SQLite (test) |
| ORM | Doctrine ORM 3.6 |
| Static Analysis | PHPStan (level max), Psalm (level 1) |
| Testing | Codeception 5.3, PHPUnit 12.5, Foundry 2.9 |
| Code Style | PHP CS Fixer 3 |
| Dev Environment | DDEV (Docker) |

## Requirements

- [Docker](https://docs.docker.com/get-started/get-docker/)
- [DDEV](https://ddev.com)

## Installation

```bash
# Clone the repository
git clone https://github.com/olecarl/api

# Install dependencies
composer install

# Configure local environment
cp .env .env.local
# Edit .env.local and set APP_SECRET and JWT_PASSPHRASE
```

## Usage

```bash
# Start the development environment
ddev start

# Open the application in browser
ddev launch

# Run database migrations
ddev console doctrine:migrations:migrate

# Open database admin (Adminer)
ddev adminer

# View project info
ddev describe
ddev console about
```

## Development

| Command | Description |
|---------|-------------|
| `ddev composer run build` | Full pipeline: prepare → lint → check → test |
| `ddev composer run prepare` | Auto-fix code style (PHP CS Fixer) |
| `ddev composer run lint` | Lint Symfony container and Twig templates |
| `ddev composer run check` | Static analysis (PHP CS Fixer, PHPStan, Psalm) |
| `ddev composer run test` | Validate Doctrine schema + Codeception tests |
| `ddev composer run report` | Generate HTML test report and PHP Metrics |

### Running Individual Test Suites

```bash
ddev exec vendor/bin/codecept run Unit
ddev exec vendor/bin/codecept run Functional
ddev exec vendor/bin/codecept run Acceptance
```

## Project Structure

```
src/
  ApiResource/   # API Platform resource classes (DTOs)
  Controller/    # Symfony controllers
  Entity/        # Doctrine entities
    Trait/       # Reusable entity traits
  Repository/    # Doctrine repositories
  Service/       # Business logic
  State/         # API Platform state providers/processors
  Security/      # Voters, authenticators
  DataFixtures/  # Database fixtures
```

## API Documentation

Swagger UI is available at [`/v1/docs`](https://api.ddev.site/v1/docs) when running in dev mode.

## Authentication

Request a JWT using a user's email address and password:

```bash
curl -X POST https://api.ddev.site/api/login_check \
    -H 'Content-Type: application/json' \
    -d '{"username":"user@example.com","password":"your-password"}'
```

Use the returned token for versioned API requests:

```bash
curl https://api.ddev.site/v1/your-resource \
    -H 'Authorization: Bearer <token>'
```
