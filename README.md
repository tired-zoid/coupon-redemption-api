# Coupon Redemption API

REST API сервис для управления промокодами и их активацией пользователями.

## Требования

- PHP 8.1 или выше
- Composer
- MySQL/PostgreSQL/SQLite
- Laravel 10 или выше

## Установка

1. Клонировать репозиторий:
   git clone <your-repository-url>
   cd coupon-redemption-api

2. Установить зависимости:
   composer install

3. Настройка окружения:
   cp .env.example .env

4. Сгенерировать ключ приложения:
   php artisan key:generate

5. Настройка базы данных (MySQL):
   Отредактируйте файл .env:
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=coupon_api
   DB_USERNAME=root
   DB_PASSWORD=

6. Запустить миграции:
   php artisan migrate

## Запуск проекта

php artisan serve
Сервер будет доступен по адресу: http://localhost:8000

## Запуск тестов

php artisan test