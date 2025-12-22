# Makefile — Symfony (FrankenPHP) + Postgres + Redis
# Użycie: make up / make bash / make cc / make migrate etc.

SHELL := /bin/bash

PUID := $(shell id -u)
PGID := $(shell id -g)

DC := docker compose
ENV_FILE := .env.docker
DC_RUN := PUID=$(PUID) PGID=$(PGID) $(DC) --env-file $(ENV_FILE)

# Nazwa serwisu aplikacji w docker-compose.yml
APP_SERVICE := app
DB_SERVICE := db
REDIS_SERVICE := redis

.PHONY: check-env
check-env: ## Sprawdź czy istnieje $(ENV_FILE)
	@test -f $(ENV_FILE) || (echo "Brak $(ENV_FILE). Utwórz go (np. na bazie .env.docker.dist)." && exit 1)

.PHONY: up
up: check-env ## Uruchom kontenery (detached)
	$(DC_RUN) up -d

.PHONY: build
build: check-env ## Zbuduj/odśwież obrazy i uruchom
	$(DC_RUN) up -d --build

.PHONY: down
down: ## Zatrzymaj i usuń kontenery
	$(DC_RUN)  down

.PHONY: restart
restart: ## Restart usług
	$(DC_RUN)  restart

.PHONY: logs
logs: ## Logi (follow)
	$(DC_RUN)  logs -f --tail=200

.PHONY: logs-app
logs-app: ## Logi aplikacji (app)
	$(DC_RUN)  logs -f --tail=200 $(APP_SERVICE)

# --- Shell / exec ---
.PHONY: bash
bash: ## Wejdź do kontenera app (shell)
	$(DC_RUN)  exec $(APP_SERVICE) sh

.PHONY: app
app: ## Alias do bash
	@$(MAKE) bash

# --- Composer ---
.PHONY: composer
composer: ## Uruchom composer (np. make composer ARGS="install")
	$(DC_RUN) exec $(APP_SERVICE) composer $(ARGS)

.PHONY: install
install: ## composer install
	$(DC_RUN) exec $(APP_SERVICE) composer install

.PHONY: update
update: ## composer update
	$(DC_RUN) exec $(APP_SERVICE) composer update

.PHONY: req
req: ## composer require (np. make req PKG="symfony/orm-pack")
	$(DC_RUN) exec $(APP_SERVICE) composer require $(PKG)

# --- Symfony console ---
.PHONY: console
console: ## Symfony console (np. make console ARGS="cache:clear")
	$(DC_RUN) exec $(APP_SERVICE) php bin/console $(ARGS)

.PHONY: cc
cc: ## cache:clear
	$(DC_RUN) exec $(APP_SERVICE) php bin/console cache:clear

# --- Database / Doctrine ---
.PHONY: db
db: ## Wejdź do psql w kontenerze db
	$(DC_RUN) exec $(DB_SERVICE) psql -U $$POSTGRES_USER -d $$POSTGRES_DB

.PHONY: migrate
migrate: ## doctrine:migrations:migrate
	$(DC_RUN) exec $(APP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction

.PHONY: mig
mig: ## doctrine:migrations:diff
	$(DC_RUN) exec $(APP_SERVICE) php bin/console doctrine:migrations:diff

# --- Redis (opcjonalne) ---
.PHONY: redis-cli
redis-cli: ## Redis CLI
	$(DC_RUN)  exec $(REDIS_SERVICE) redis-cli

.PHONY: redis-flush
redis-flush: ## FLUSHALL (UWAGA: czyści Redis)
	$(DC_RUN)  exec $(REDIS_SERVICE) redis-cli FLUSHALL

# --- QA / Tests ---
.PHONY: test
test: ## PHPUnit (jeśli masz)
	$(DC_RUN)  exec $(APP_SERVICE) php bin/phpunit $(ARGS)

.PHONY: cs
cs: ## PHP-CS-Fixer (jeśli masz w vendor/bin)
	$(DC_RUN)  exec $(APP_SERVICE) ./vendor/bin/php-cs-fixer fix -v

# --- Clean ---
.PHONY: clean
clean: ## Zatrzymaj, usuń kontenery i wolumeny (DB też!)
	$(DC_RUN)  down -v
