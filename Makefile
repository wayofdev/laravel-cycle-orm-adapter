-include .env

# BuildKit enables higher performance docker builds and caching possibility
# to decrease build times and increase productivity for free.
# https://docs.docker.com/compose/environment-variables/envvars/
export DOCKER_BUILDKIT ?= 1

# Docker binary to use, when executing docker tasks
DOCKER ?= docker

# Binary to use, when executing docker-compose tasks
DOCKER_COMPOSE ?= $(DOCKER) compose

# Support image with all needed binaries, like envsubst, mkcert, wait4x
SUPPORT_IMAGE ?= wayofdev/build-deps:alpine-latest

APP_RUNNER ?= $(DOCKER_COMPOSE) run --rm --no-deps app
APP_COMPOSER ?= $(APP_RUNNER) composer

BUILDER_PARAMS ?= $(DOCKER) run --rm -i \
	--env-file ./.env \
	--env COMPOSE_PROJECT_NAME=$(COMPOSE_PROJECT_NAME) \
	--env COMPOSER_AUTH="$(COMPOSER_AUTH)"

BUILDER ?= $(BUILDER_PARAMS) $(SUPPORT_IMAGE)
BUILDER_WIRED ?= $(BUILDER_PARAMS) --network project.$(COMPOSE_PROJECT_NAME) $(SUPPORT_IMAGE)

# Shorthand envsubst command, executed through build-deps
ENVSUBST ?= $(BUILDER) envsubst

# Yamllint docker image
YAML_LINT_RUNNER ?= $(DOCKER) run --rm $$(tty -s && echo "-it" || echo) \
	-v $(PWD):/data \
	cytopia/yamllint:latest \
	-c ./.github/.yamllint.yaml \
	-f colored .

ACTION_LINT_RUNNER ?= $(DOCKER) run --rm $$(tty -s && echo "-it" || echo) \
	-v $(shell pwd):/repo \
	 --workdir /repo \
	 rhysd/actionlint:latest \
	 -color

MARKDOWN_LINT_RUNNER ?= $(DOCKER) run --rm $$(tty -s && echo "-it" || echo) \
	-v $(shell pwd):/app \
	--workdir /app \
	davidanson/markdownlint-cli2-rules:latest

PHIVE_RUNNER ?= $(DOCKER_COMPOSE) run --rm --no-deps app

NPM_RUNNER ?= pnpm

EXPORT_VARS = '\
	$${COMPOSE_PROJECT_NAME} \
	$${COMPOSER_AUTH}'

#
# Self documenting Makefile code
# ------------------------------------------------------------------------------------
ifneq ($(TERM),)
	BLACK := $(shell tput setaf 0)
	RED := $(shell tput setaf 1)
	GREEN := $(shell tput setaf 2)
	YELLOW := $(shell tput setaf 3)
	LIGHTPURPLE := $(shell tput setaf 4)
	PURPLE := $(shell tput setaf 5)
	BLUE := $(shell tput setaf 6)
	WHITE := $(shell tput setaf 7)
	RST := $(shell tput sgr0)
else
	BLACK := ""
	RED := ""
	GREEN := ""
	YELLOW := ""
	LIGHTPURPLE := ""
	PURPLE := ""
	BLUE := ""
	WHITE := ""
	RST := ""
endif
MAKE_LOGFILE = /tmp/wayofdev-laravel-cycle-orm-adapter.log
MAKE_CMD_COLOR := $(BLUE)

default: all

help: ## Show this menu
	@echo 'Management commands for package:'
	@echo 'Usage:'
	@echo '    ${MAKE_CMD_COLOR}make${RST}                       Setups dependencies for fresh-project, like composer install, git hooks and others...'
	@grep -E '^[a-zA-Z_0-9%-]+:.*?## .*$$' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "    ${MAKE_CMD_COLOR}make %-21s${RST} %s\n", $$1, $$2}'
	@echo
	@echo '    📑 Logs are stored in      $(MAKE_LOGFILE)'
	@echo
	@echo '    📦 Package                 laravel-cycle-orm-adapter (github.com/wayofdev/laravel-cycle-orm-adapter)'
	@echo '    🤠 Author                  Andrij Orlenko (github.com/lotyp)'
	@echo '    🏢 ${YELLOW}Org                     wayofdev (github.com/wayofdev)${RST}'
	@echo
.PHONY: help

.EXPORT_ALL_VARIABLES:

#
# Default action
# Defines default command when `make` is executed without additional parameters
# ------------------------------------------------------------------------------------
all: env prepare install hooks phive up
.PHONY: all

#
# System Actions
# ------------------------------------------------------------------------------------
env: ## Generate .env file from example, use `make env force=true`, to force re-create file
ifeq ($(FORCE),true)
	@echo "${YELLOW}Force re-creating .env file from example...${RST}"
	$(ENVSUBST) $(EXPORT_VARS) < ./.env.example > ./.env
else ifneq ("$(wildcard ./.env)","")
	@echo ""
	@echo "${YELLOW}The .env file already exists! Use FORCE=true to re-create.${RST}"
else
	@echo "Creating .env file from example"
	$(ENVSUBST) $(EXPORT_VARS) < ./.env.example > ./.env
endif
.PHONY: env

prepare:
	mkdir -p .build/php-cs-fixer
.PHONY: prepare

#
# Docker Actions
# ------------------------------------------------------------------------------------
up: # Creates and starts containers, defined in docker-compose and override file
	$(DOCKER_COMPOSE) up --remove-orphans -d
.PHONY: up

down: # Stops and removes containers of this project
	$(DOCKER_COMPOSE) down --remove-orphans --volumes
.PHONY: down

restart: down up ## Runs down and up commands
.PHONY: restart

clean: ## Stops containers if required and removes from system
	$(DOCKER_COMPOSE) rm --force --stop
.PHONY: clean

ps: ## List running project containers
	$(DOCKER_COMPOSE) ps
.PHONY: ps

logs: ## Show project docker logs with follow up mode enabled
	$(DOCKER_COMPOSE) logs -f
.PHONY: logs

pull: ## Pull and update docker images in this project
	$(DOCKER_COMPOSE) pull
.PHONY: pull

ssh: ## Login inside running docker container
	$(APP_RUNNER) sh
.PHONY: ssh

#
# Composer
# ------------------------------------------------------------------------------------
install: ## Installs composer dependencies
	$(APP_COMPOSER) install
.PHONY: install

update: ## Updates composer dependencies by running composer update command
	$(APP_COMPOSER) update
.PHONY: update

phive: ## Installs dependencies with phive
	$(APP_RUNNER) /usr/local/bin/phive install --trust-gpg-keys 0xC00543248C87FB13,0x033E5F8D801A2F8D
.PHONY: phive

#
# Code Quality, Git, Linting
# ------------------------------------------------------------------------------------
hooks: ## Install git hooks from pre-commit-config
	pre-commit install
	pre-commit install --hook-type commit-msg
	pre-commit autoupdate
.PHONY: hooks

lint: lint-yaml lint-actions lint-md lint-php lint-stan lint-composer lint-audit ## Runs all linting commands
.PHONY: lint

lint-yaml: ## Lints yaml files inside project
	@$(YAML_LINT_RUNNER) | tee -a $(MAKE_LOGFILE)
.PHONY: lint-yaml

lint-actions: ## Lint all github actions
	@$(ACTION_LINT_RUNNER) | tee -a $(MAKE_LOGFILE)
.PHONY: lint-actions

lint-md: ## Lint all markdown files using markdownlint-cli2
	@$(MARKDOWN_LINT_RUNNER) --fix "**/*.md" "!CHANGELOG.md" "!vendor" "!docs/node_modules" | tee -a $(MAKE_LOGFILE)
.PHONY: lint-md

lint-md-dry: ## Lint all markdown files using markdownlint-cli2 in dry-run mode
	@$(MARKDOWN_LINT_RUNNER) "**/*.md" "!CHANGELOG.md" "!vendor" "!docs/node_modules" | tee -a $(MAKE_LOGFILE)
.PHONY: lint-md-dry

lint-php: prepare ## Fixes code to follow coding standards using php-cs-fixer
	$(APP_COMPOSER) cs:fix
.PHONY: lint-php

lint-diff: prepare ## Runs php-cs-fixer in dry-run mode and shows diff which will by applied
	$(APP_COMPOSER) cs:diff
.PHONY: lint-diff

lint-stan: ## Runs phpstan – static analysis tool
	$(APP_COMPOSER) stan
.PHONY: lint-stan

lint-stan-ci: ## Runs phpstan – static analysis tool with github output (CI mode)
	$(APP_COMPOSER) stan:ci
.PHONY: lint-stan-ci

lint-stan-baseline: ## Runs phpstan to update its baseline
	$(APP_COMPOSER) stan:baseline
.PHONY: lint-stan-baseline

lint-psalm: ## Runs vimeo/psalm – static analysis tool
	$(APP_COMPOSER) psalm
.PHONY: lint-psalm

lint-psalm-ci: ## Runs vimeo/psalm – static analysis tool with github output (CI mode)
	$(APP_COMPOSER) psalm:ci
.PHONY: lint-psalm-ci

lint-psalm-baseline: ## Runs vimeo/psalm to update its baseline
	$(APP_COMPOSER) psalm:baseline
.PHONY: lint-psalm-baseline

lint-deps: ## Runs composer-require-checker – checks for dependencies that are not used
	$(APP_RUNNER) .phive/composer-require-checker check \
		--config-file=/app/composer-require-checker.json \
		--verbose
.PHONY: lint-deps

lint-composer: ## Normalize composer.json and composer.lock files
	$(APP_RUNNER) .phive/composer-normalize normalize
.PHONY: lint-composer

lint-audit: ## Runs security checks for composer dependencies
	$(APP_COMPOSER) audit
.PHONY: lint-security

refactor: ## Runs rector – code refactoring tool
	$(APP_COMPOSER) refactor
.PHONY: refactor

#
# Testing
# ------------------------------------------------------------------------------------
infect: ## Runs mutation tests with infection/infection
	$(APP_COMPOSER) infect
.PHONY: infect

infect-ci: ## Runs infection – mutation testing framework with github output (CI mode)
	$(APP_COMPOSER) infect:ci
.PHONY: lint-infect-ci

test: ## Run project php-unit and pest tests against sqlite in-memory database
	$(APP_COMPOSER) test
.PHONY: test

test-pgsql: ## Run project php-unit and pest tests over pgsql database
	$(APP_COMPOSER) test:pgsql
.PHONY: test-pgsql

test-mysql: ## Run project php-unit and pest tests over mysql database
	$(APP_COMPOSER) test:mysql
.PHONY: test-mysql

test-sqlite: ## Run project php-unit and pest tests over sqlite in-file database
	$(APP_COMPOSER) test:sqlite
.PHONY: test-sqlite

test-sqlserver: ## Run project php-unit and pest tests over mssql (sqlserver) database
	$(APP_COMPOSER) test:sqlserver
.PHONY: test-sqlserver

test-cc: ## Run project php-unit and pest tests in coverage mode and build report
	$(APP_COMPOSER) test:cc
.PHONY: test-cc

#
# Release
# ------------------------------------------------------------------------------------
commit:
	czg commit --config="./.github/.cz.config.js"
.PHONY: commit

#
# Documentation
# Only in case, when `./docs` folder exists and has package.json
# ------------------------------------------------------------------------------------
docs-deps-update: ## Check for outdated dependencies and automatically update them using pnpm
	cd docs && $(NPM_RUNNER) run deps:update
.PHONY: docs-deps-update

docs-deps-install: ## Install dependencies for documentation using pnpm
	cd docs && $(NPM_RUNNER) install
.PHONY: docs-deps-install

docs-up: ## Start documentation server
	cd docs && $(NPM_RUNNER) dev
.PHONY: docs-up

docs-build: ## Build documentation
	cd docs && $(NPM_RUNNER) build
.PHONY: docs-up
