# 2 Start project
require:
* docker
* composer
* symfony cli

## 2.1. Установка зависимости для работы с базой данных.

```
# composer require symfony/orm-pack

 doctrine/doctrine-bundle  instructions:

  * Modify your DATABASE_URL config in .env

  * Configure the driver (postgresql) and
    server_version (15) in config/packages/doctrine.yaml

```
Требуется создать файл **docker-compose** в нем прописать:
```yaml
version: "3.9"

services:
  database:
    container_name: postgres
    image: postgres:13.3-alpine
    environment:
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: 123123
      POSTGRES_HOST_AUTH_METHOD: trust
    ports:
      - "5433:5432"
```
Для использования атрибутов требуется изменить конфигурацию **doctrine**. В
файле конфигурации **config/packages/doctrine.yaml** добавить свойство:
```yaml
doctrine.orm.mappings.App.type: attribute
```

## 2.2. Maker bundle

**Symfony Maker** помогает вам создавать пустые команды, контроллеры, классы
форм, тесты и многое другое, так что вы можете забыть о написании шаблонного
кода. Этот пакет является альтернативой **SensioGeneratorBundle** для
современных приложений **Symfony** и требует использования **Symfony 3.4**
или новее. Этот пакет предполагает, что вы используете стандартную структуру
каталогов **Symfony 4**, но многие команды могут генерировать код в любом
приложении.
```
composer require --dev symfony/maker-bundle
```
Использование:
```
./bin/console list make

./bin/console make:entity
```

Документация https://symfony.com/bundles/SymfonyMakerBundle/current/index.html

## 2.1. Symfony
Создание локальных переменных:
```
symfony var:export --multiline > .env.local
```

Создание миграции:
* `./bin/console make:migration`
* `./bin/console doctrine:migrations:diff` - проверяет базу данных и сущности,
если есть расхождения тогда создаёт миграцию.


Выполнить миграцию:
`./bin/console doctrine:migrations:migrate`

Запустить сервер:
`symfony serve`

## 2.2. analyze your code
`vendor/bin/phpstan analyse src`

## 2.3. linter
`./vendor/bin/php-cs-fixer fix`

---

# 3 Create first service
## 3.1. Создание тестовых данных в БД
Установка зависимостей: `composer require --dev orm-fixtures`

Исользуем fixtures:
* `./bin/console make:fixtures`
* `# The class name of the fixtures to create (e.g. AppFixtures):`
* `# BookCategoryFixtures`
* `./bin/console doctrine:fixtures:load --purge-with-truncate` - перед загрузкой делаем truncate

## 3.2. UNIT Тестирование
Докментация: `https://docs.phpunit.de/en/10.3/`

Настройка(**временно, так делать нельзя**):
* Закоментировать в файле `config/packages/doctrine.yaml` строку
`when@test.doctrine.dbal.dbname_suffix`. Это делается для того, что бы в тестах
использовалась dev база данных

Установка зависимостей:
* `composer require --dev phpunit/phpunit` - бибилиотека
* `composer require --dev symfony/test-pack` - хелперы symfony

## 3.3. Swagger
Документация приложения доступна по `${host}:${port}/api/doc`

Зависимости:
* `composer require doctrine/annotations` - в php уже есть функционал аттрибутов,
но не все библиотеки могут с ними работать. Некоторые библиотеки еще работают
только с аннотациями.
* `composer require -W nelmio/api-doc-bundle`
* `composer require twig`
* `composer require asset`

# 4. Receiving books by category
Добавлены UNIT и FUNC тесты, добавлен контроллер и сервис.

# 5. More tests
Требуемые зависимости:

**Doctrine test bundle** - Этот пакет предоставляет функции, которые помогут вам
более эффективно запускать набор тестов вашего приложения на основе Symfony
с помощью изолированных тестов.

https://github.com/dmaicher/doctrine-test-bundle

`composer require --dev dama/doctrine-test-bundle`

Также требуется добавить extension в файл конфигурации PHPUnit:
```xml
<extensions>
    <extension class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension" />
</extensions>
```

Еще одна зависимость:

**phpunit-json-assert** - Эта библиотека добавляет в PHPUnit несколько новых утверждений, которые
позволяют легко и лаконично проверять сложные структуры данных (часто, но не
обязательно, документы JSON) с использованием выражений JSONPath и схем JSON.

https://packagist.org/packages/helmich/phpunit-json-assert

`composer require --dev helmich/phpunit-json-assert`

# 6 Processing exception

# 7 Test exception
7.1. Установка monolog

`composer require symfony/monolog-bundle`

7.2. Настройка monolog для dev (файл **config.packages.monolog.yaml**):
```yaml
when@dev:
    monolog:
        handlers:
            main:
                type: stream
                path: "php://stderr"
                level: debug
                channels: ["!event"]
```

7.3. Отключить ошибку о favicon.ico

```phpt
php -r "copy('https://symfony.com/favicon.ico', 'public/favicon.ico');"
```

7.4. Добавить параметр в контекст(autowired)

Объявляем перенную **$isDebug** в файле **config/service.yaml**, эта переменная
будет доступна в через **autowired**:
```yaml
services:
    _defaults:
        bind:
            bool $isDebug: '%kernel.debug%'
```
Режим дебага отключается в **prod** профиле. Для того что бы указать **prod**
режим надо добавить строку "**export APP_ENV=prod**" в файл **env.local**

# 8 Subscribe
Установка валидатора:

`composer require symfony/validator`
