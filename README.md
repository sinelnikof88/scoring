# Scoring App

## Требования

- Docker, Docker Compose

## Запуск

1. docker compose up -d
2. docker exec -it scoring_php bash
3. cd /app/scoring
4. composer install
5. php bin/console doctrine:database:create --if-not-exists
6. php bin/console doctrine:migrations:migrate --no-interaction
7. php bin/console doctrine:fixtures:load --no-interaction
8. Открыть http://localhost:8080

## Тесты

php bin/phpunit

## Консольная команда

php bin/console app:scoring:calculate # все клиенты
php bin/console app:scoring:calculate 1 # один клиент

## Стек

- PHP 8.3, Symfony 7.x
- MariaDB 11
- Pagerfanta (пагинация)