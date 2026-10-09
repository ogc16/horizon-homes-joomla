SHELL := /bin/bash
COMPOSE := docker compose

.DEFAULT_GOAL := help

.PHONY: help build up down restart logs smoke php-lint lint-php lint-shell lint-docker lint-editorconfig lint-yaml lint phpstan test clean

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-18s %s\n", $$1, $$2}'

build: ## Build the application image
	$(COMPOSE) build

up: ## Start the stack (build if needed) and wait for health
	$(COMPOSE) up -d --build --wait

down: ## Stop the stack and remove volumes
	$(COMPOSE) down -v

restart: down up ## Recreate the stack

logs: ## Tail the container logs
	$(COMPOSE) logs -f --tail=100

smoke: ## Run the end-to-end smoke test against a running stack
	bash .github/scripts/smoke.sh

php-lint: ## Syntax-check all PHP files
	find components -name '*.php' -print0 | xargs -0 -n1 php -l

lint-php: ## Run PHPCS (PSR-12)
	phpcs --standard=phpcs.xml.dist

lint-shell: ## Run ShellCheck via Docker
	docker run --rm -v "$$PWD:/mnt" -w /mnt koalaman/shellcheck:stable docker/entrypoint.sh .github/scripts/smoke.sh

lint-docker: ## Run Hadolint via Docker
	docker run --rm -v "$$PWD:/app" -w /app --entrypoint hadolint hadolint/hadolint Dockerfile

lint-editorconfig: ## Run editorconfig-checker via Docker
	docker run --rm -v "$$PWD:/check" -w /check mstruebing/editorconfig-checker

lint-yaml: ## Run yamllint via Docker
	docker run --rm -v "$$PWD:/data" -w /data cytopia/yamllint -c .yamllint.yaml docker-compose.yml .github

lint: php-lint lint-php lint-shell lint-docker lint-editorconfig lint-yaml ## Run every linter

phpstan: ## Run PHPStan inside the running stack
	@test -f phpstan.phar || curl -fsSL https://github.com/phpstan/phpstan/releases/latest/download/phpstan.phar -o phpstan.phar
	$(COMPOSE) cp phpstan.phar joomla:/tmp/phpstan.phar
	$(COMPOSE) cp phpstan.neon joomla:/var/www/html/phpstan.neon
	$(COMPOSE) cp phpstan-baseline.neon joomla:/var/www/html/phpstan-baseline.neon
	$(COMPOSE) cp phpstan-bootstrap.php joomla:/var/www/html/phpstan-bootstrap.php
	$(COMPOSE) exec -T -w /var/www/html joomla php /tmp/phpstan.phar analyse -c phpstan.neon --no-progress

test: smoke ## Alias for smoke

clean: ## Remove generated artifacts
	rm -f phpstan.phar
