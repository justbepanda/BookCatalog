ifneq (,$(wildcard ./.env))
    include .env
    export
endif

DB_USER ?= books_user
DB_PASSWORD ?= secret
DB_NAME ?= books

.PHONY: help init up down restart shell composer-install composer-update webapp migrate migrate-create

SERVICE=php

help:
	@echo "Commands:"
	@echo " make init              - build containers, install composer, create Yii1 webapp, migrate"
	@echo " make up                - start containers"
	@echo " make down              - stop containers"
	@echo " make restart           - restart php container"
	@echo " make shell             - enter php container"
	@echo " make composer-install  - install composer dependencies"
	@echo " make composer-update   - update composer dependencies"
	@echo " make webapp            - create Yii1 webapp"
	@echo " make migrate           - run migrations"
	@echo " make migrate-create    - create new migration"

init:
	@if [ ! -f .env ]; then cp .env.example .env; echo ".env created from example"; fi
	docker compose down -v
	docker compose up -d --build
	@echo "Waiting for MySQL to initialize (this can take 10-15 seconds)..."
	@until docker compose exec db mysql -u$(DB_USER) -p$(DB_PASSWORD) -e "select 1" > /dev/null 2>&1; do \
		echo "MySQL is still initializing... sleeping 2s"; \
		sleep 2; \
	done
	@echo "MySQL is READY!"
	docker compose exec $(SERVICE) composer install
	@if [ ! -d "protected" ]; then \
		echo "yes" | docker compose exec -T $(SERVICE) php vendor/yiisoft/yii/framework/yiic.php webapp .; \
	fi
	docker compose exec $(SERVICE) php protected/yiic.php migrate --interactive=0
	@echo "Project is ready at http://localhost:8080"

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart $(SERVICE)

shell:
	docker compose exec $(SERVICE) bash

composer-install:
	docker compose exec $(SERVICE) composer install

composer-update:
	docker compose exec $(SERVICE) composer update

webapp:
	docker compose exec $(SERVICE) php vendor/yiisoft/yii/framework/yiic.php webapp .

migrate:
	docker compose exec $(SERVICE) php protected/yiic.php migrate --interactive=0

migrate-create:
	docker compose exec $(SERVICE) php protected/yiic.php migrate create
