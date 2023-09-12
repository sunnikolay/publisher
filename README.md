# 1 Start project
require:
* docker
* composer
* symfony cli

## Symfony
Создание локальных переменных:
`symfony var:export --multiline > .env.local`

Создание сущностей:
`./bin/console make:entity`

Создание миграции:
`./bin/console make:migration`

Выполнить миграцию:
`./bin/console doctrine:migrations:migrate`

Запустить сервер:
`symfony serve`

## analyze your code
`vendor/bin/phpstan analyse src`

## linter
`./vendor/bin/php-cs-fixer fix`

## Doctrine
