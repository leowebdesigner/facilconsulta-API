SHELL := /bin/bash
COMPOSE := docker compose

.PHONY: build up down restart logs bash migrate seed test fix optimize swagger key

build:
	$(COMPOSE) build

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

restart:
	$(COMPOSE) down && $(COMPOSE) up -d

logs:
	$(COMPOSE) logs -f

bash:
	$(COMPOSE) exec php bash

migrate:
	$(COMPOSE) exec php php artisan migrate --force

seed:
	$(COMPOSE) exec php php artisan db:seed --force

test:
	$(COMPOSE) exec php php artisan test

fix:
	$(COMPOSE) exec php ./vendor/bin/pint

optimize:
	$(COMPOSE) exec php php artisan optimize

swagger:
	$(COMPOSE) exec php php artisan l5-swagger:generate

key:
	$(COMPOSE) exec php php artisan key:generate
