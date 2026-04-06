# Restful Web API Framework

[![Symfony](https://github.com/olecarl/api/actions/workflows/symfony.yml/badge.svg?branch=master)](https://github.com/olecarl/api/actions/workflows/symfony.yml)

REST API using API-Platform distribution of Symfony PHP Framework.
Local Development Environment managed by DDEV using DOCKER containers.

## Features

- Content Negotiation
- Semantic Versioning
- Continuous Integration
- Automated Testing
- CORS
- OpenAPI 3.0

## Stack

- PHP 8.5
- Symfony 8.0
- API Platform 4.2
- Doctrine ORM 3.6
- PHPStan 2.1 + Psalm 6
- Codeception 5.3
- PHP CS Fixer 3
- PHP Metrics 2.9
- PHPUnit 12.5
- Foundry 2.9

## Requirements

- [DOCKER](https://docs.docker.com/get-started/get-docker/)
- [DDEV](https://ddev.com)

## Installation

Clone the project from GitHub: `git clone https://github.com/olecarl/api`

Install required PHP dependencies: `composer install`

## Usage

Create and start the project in docker containers: `ddev start`

Launch a browser with the current site: `ddev launch`

See project information: `ddev describe`, `ddev console about`

Run Doctrine Migrations: `ddev console doctrine:migrations:migrate`

Launch adminer database management tool in browser: `ddev adminer`

## Development

| Command | Description |
|---|---|
| `ddev composer run build` | Full pipeline: format → lint → static analysis → test |
| `ddev composer run prepare` | Auto-fix code style (PHP CS Fixer) |
| `ddev composer run lint` | Lint Symfony container and Twig templates |
| `ddev composer run check` | Static analysis (PHP CS Fixer dry-run, PHPStan, Psalm) |
| `ddev composer run test` | Validate Doctrine schema + run Codeception test suites |
| `ddev composer run report` | Generate HTML test report and PHP Metrics report |

## API Documentation

Swagger UI is available at [`/api/docs`](https://api.ddev.site/docs) when running in dev mode.
