# 1 Start project
require:
* docker
* composer
* symfony cli

## 1.1. Symfony
Создание локальных переменных:
`symfony var:export --multiline > .env.local`

Создание сущностей:
`./bin/console make:entity`

Создание миграции:
* `./bin/console make:migration`
* `./bin/console doctrine:migrations:diff` - проверяет базу данных и сущности,
если есть расхождения тогда создаёт миграцию.


Выполнить миграцию:
`./bin/console doctrine:migrations:migrate`

Запустить сервер:
`symfony serve`

## 1.2. analyze your code
`vendor/bin/phpstan analyse src`

## 1.3. linter
`./vendor/bin/php-cs-fixer fix`

# 2 Create first service
## 2.1. Создание тестовых данных в БД
Установка зависимостей: `composer require --dev orm-fixtures`

Исользуем fixtures:
* `./bin/console make:fixtures`
* `# The class name of the fixtures to create (e.g. AppFixtures):`
* `# BookCategoryFixtures`
* `./bin/console doctrine:fixtures:load --purge-with-truncate` - перед загрузкой делаем truncate

## 2.2. UNIT Тестирование
Докментация: `https://docs.phpunit.de/en/10.3/`

Настройка(**временно, так делать нельзя**):
* Закоментировать в файле `config/packages/doctrine.yaml` строку
`when@test.doctrine.dbal.dbname_suffix`. Это делается для того, что бы в тестах
использовалась dev база данных

Установка зависимостей:
* `composer require --dev phpunit/phpunit` - бибилиотека
* `composer require --dev symfony/test-pack` - хелперы symfony

## 2.3. Swagger
Документация приложения доступна по `${host}:${port}/api/doc`

Зависимости:
* `composer require doctrine/annotations` - в php уже есть функционал аттрибутов,
но не все библиотеки могут с ними работать. Некоторые библиотеки еще работают
только с аннотациями.
* `composer require -W nelmio/api-doc-bundle`
* `composer require twig`
* `composer require asset`

# 3. Receiving books by category
Добавлены UNIT и FUNC тесты, добавлен контроллер и сервис.
