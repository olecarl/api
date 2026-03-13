# Restful Web API Framework

REST API using API-Platform distribution of Symfony PHP Framework.
Local Development Environment managed by DDEV using DOCKER containers.

## Features

- Symfony 7.4
- API Platform 4.2
- PHPStan 2.1
- PHPUnit 12.5
- Codeception 5.3
- PhpMetrics v2.9

## Requirements

- [DOCKER](https://docs.docker.com/get-started/get-docker/)
- [DDEV](https://ddev.com)

## Installation

Clone the project from GitHub: `git clone https://github.com/olecarl/api`

Install required PHP dependencies: `ddev composer install`

## Usage

Create and start the project in docker containers with: `ddev start` 

Launch a browser with the current site: `ddev launch`

See project information with: `ddev describe`, `ddev console about` 

Run Doctrine Migrations: `ddev console doctrine:migrations:migrate`

Launch adminer database management tool in browser: `ddev adminer`

## Testing

Run Codeception testsuite: `ddev composer run test`

## Code Inspection

Run Static code analysis: `ddev composer run inspect`

See Composer scripts in `composer.json` for more testing and analysis tools.
