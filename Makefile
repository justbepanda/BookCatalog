ifneq (,$(wildcard ./.env))
    include .env
    export
endif

DB_USER ?= books_user
DB_PASSWORD ?= secret
DB_NAME ?= books

.PHONY: help init up down restart shell composer-install composer-update webapp migrate migrate-create seeds test-prep test

SERVICE=php

help:
	@echo "========================================================================"
	@echo "Управление проектом (Yii 1.1 + Docker)"
	@echo "========================================================================"
	@echo " make init              - Полная инициализация проекта с нуля"
	@echo " make up                - Запустить контейнеры"
	@echo " make down              - Остановить и удалить контейнеры"
	@echo " make restart           - Перезапустить PHP контейнер"
	@echo " make shell             - Войти в терминал PHP контейнера"
	@echo ""
	@echo "Работа с зависимостями:"
	@echo " make composer-install  - Установить зависимости из composer.lock"
	@echo " make composer-update   - Обновить зависимости (composer.json)"
	@echo ""
	@echo "Работа с базой данных (Основная):"
	@echo " make webapp            - Создать структуру приложения Yii"
	@echo " make migrate           - Запустить миграции"
	@echo " make migrate-create    - Создать новую миграцию"
	@echo " make seeds             - Наполнить базу тестовыми данными"
	@echo ""
	@echo "Тестирование (PHPUnit 9):"
	@echo " make test-prep         - Создать и синхронизировать БД для тестов"
	@echo " make test              - Запустить все тесты проекта"
	@echo " make test-unit         - Запустить только Unit-тесты"
	@echo " make test-file file=X  - Запустить конкретный тест (например, file=BookTest.php)"
	@echo "========================================================================"

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
	docker compose exec $(SERVICE) php protected/yiic.php seed run --fresh=1
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

seeds:
	docker compose exec $(SERVICE) php protected/yiic.php seed run --fresh=1

test-prep:
	@echo "Creating database for tests..."
	docker compose exec db sh -c 'mysql -u root -p"$$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS yii_db_test;"'
	@echo "Copying data from yii_db to yii_db_test..."
	docker compose exec db sh -c 'mysqldump -u root -p"$$MYSQL_ROOT_PASSWORD" --no-data $$MYSQL_DATABASE | mysql -u root -p"$$MYSQL_ROOT_PASSWORD" yii_db_test'
	@echo "yii_db_test is ready!"

test:
	docker compose exec php ./vendor/bin/phpunit -c protected/tests/phpunit.xml protected/tests/

test-unit:
	docker compose exec php ./vendor/bin/phpunit -c protected/tests/phpunit.xml protected/tests/unit/

test-file:
	docker compose exec php ./vendor/bin/phpunit -c protected/tests/phpunit.xml protected/tests/unit/$(file)