# Scoring App

Web-приложение для расчёта скоринга клиентов на Symfony.

## Задача

- Регистрация клиентов с валидацией российского номера телефона
- Список клиентов со скорингом и пагинацией
- Карточка клиента: просмотр и редактирование
- Автоматический расчёт скоринга по 4 правилам
- Консольная команда для пересчёта скоринга (все клиенты / по id)
- Фикстуры для быстрого заполнения БД
- Покрытие тестами бизнес-логики

## Правила скоринга

| Правило                      | Значение    | Баллы |
|------------------------------|-------------|-------|
| Сотовый оператор             | МегаФон     | 10    |
|                              | Билайн      | 5     |
|                              | МТС         | 3     |
|                              | Иной        | 1     |
| Домен э-почты                | gmail       | 10    |
|                              | yandex      | 8     |
|                              | mail        | 6     |
|                              | Иной        | 3     |
| Образование                  | Высшее      | 15    |
|                              | Специальное | 10    |
|                              | Среднее     | 5     |
| Согласие на обработку данных | Да          | 4     |
|                              | Нет         | 0     |

**Итоговый скоринг** — сумма баллов по всем правилам.

## Стек

- PHP 8.3
- Symfony 7.x
- MariaDB 11
- Doctrine ORM + Migrations
- Twig
- Symfony Forms + Validator
- Pagerfanta
- PHPUnit 12
- Docker / Docker Compose

## Требования

- Docker
- Docker Compose
- SSH-доступ к хосту с Docker (для удалённой разработки)

## Запуск

### 1. Клонировать репозиторий и запустить контейнеры

```bash
git clone https://github.com/sinelnikof88/scoring
cd scoring
docker compose up -d
```

### 2. Установить зависимости

```bash
docker exec -it scoring_php bash
cd /app/scoring
composer install
```

### 3. Подготовить базу данных

```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

### 4. Загрузить тестовые данные

```bash
php bin/console doctrine:fixtures:load --no-interaction
```
### 5. Открыть приложение

http://localhost:8080

Тесты

```bash
docker exec -it scoring_php bash
cd /app/scoring
php bin/phpunit
```

Ожидаемый результат: 49 tests, 57 assertions — OK.

Запуск конкретной группы:


```bash
php bin/phpunit tests/Service/Scoring
```
Консольная команда
Пересчёт скоринга по всем клиентам:

```bash
php bin/console app:scoring:calculate
```

Пересчёт по одному клиенту (по id):

```bash
php bin/console app:scoring:calculate 1
```

### Команда обновляет скоринг в БД и выводит детализацию по правилам:

```text

Клиент #1: ыва ыва (asd@sf.d)
-----------------------------

 ------------------------------ -------
  Правило                        Баллы
 ------------------------------ -------
  Образование                    +5
  Домен э-почты                  +3
  Сотовый оператор               +3
  Согласие на обработку данных   +4
  ИТОГО                          15
 ------------------------------ -------

```

### Структура проекта

```text
src/
├── Command/
│   └── CalculateScoringCommand.php   # консольная команда
├── Controller/
│   └── ClientController.php          # регистрация, список, карточка, редактирование
├── DataFixtures/
│   └── ClientFixtures.php            # тестовые данные
├── Entity/
│   └── Client.php                    # сущность клиента
├── Enum/
│   └── Education.php                 # enum уровня образования
├── Form/
│   └── ClientRegistrationType.php    # форма регистрации / редактирования
├── Repository/
│   └── ClientRepository.php
└── Service/
    └── Scoring/
    ├── ScoringService.php        # агрегатор правил
    └── Type/
        ├── ScoringRuleInterface.php
        ├── Phone.php             # правило: сотовый оператор
        ├── Email.php             # правило: домен э-почты
        ├── Education.php         # правило: образование
        └── ProcessingConsent.php # правило: согласие

tests/
└── Service/
    └── Scoring/
        ├── ScoringServiceTest.php
        └── ProcessingConsentTest.php
        └── Type/
            ├── PhoneTest.php
            ├── EmailTest.php
            └── EducationTest.php
```

### Остановка

```bash
docker compose down
```

Удалить также данные БД:

``` bash
docker compose down -v
```

Разработка
Запуск PHP CS Fixer:

```bash
vendor/bin/php-cs-fixer fix
 ```

Проверка без изменений:

```bash
vendor/bin/php-cs-fixer fix --dry-run --diff
``` 