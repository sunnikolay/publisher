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
Documentation: https://phpstan.org/user-guide/getting-started
```
composer require --dev phpstan/phpstan
```
```
vendor/bin/phpstan analyse src
```

## 2.3. linter - setup
Documentation: https://cs.symfony.com
```
composer require --dev friendsofphp/php-cs-fixer
```
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

# 12 Integration with external services
Зависимость - **hoverfly**

**Hoverfly** — это автоматизированный инструмент моделирования связи API с
открытым исходным кодом, который помогает в интеграционном тестировании.
Пользователь может проверить, как API реагируют на определенные события,
такие как сетевая задержка и ограничение скорости.

Документация:
https://docs.hoverfly.io/en/latest/pages/keyconcepts/templating/templating.html

Настройка **hoverfly**:

1) Создать папку - hoverfly/responses/recommend
2) Создать папку - hoverfly/simulations
3) Добавить image в docker-compose:
```yaml
  hoverfly:
    container_name: hoverfly
    image: spectolabs/hoverfly:v1.3.4
    command:
      - "-webserver"
      - "-response-body-files-path=/hoverfly_app/responses"
      - "-import=/hoverfly_app/simulations/recommend.simulations.json"
    volumes:
      - "$PWD/hoverfly:/hoverfly_app:ro"
    ports:
      - "8500:8500"
```
В тестировании можно использовать библиотеку автора:
```
composer require --dev ns3777k/hoverfly
```

Зависимость - **HTTP Client**

```
composer require symfony/http-client
```

Настроить DI в файле `config/packages/framework.yaml`:
```yaml
framework:
  http_client:
    scoped_clients:
      recommendation.client:
        base_uri: '%env(RECOMMENDATION_SVC_URL)%'
        headers:
          Accept: 'application/json'
          Authorization: 'Bearer %env(RECOMMENDATION_SVC_TOKEN)%'
```

# 15 Authentication

## 1. Установка
Документация: https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/index.rst#getting-started

Необходимые зависимости:
```
composer require lexik/jwt-authentication-bundle
```
После установки сработает рецепт который сгенерирует два файла, это security.yaml
и lexik_jwt_authentication.yaml.

Также при установке lexik установится bundle security. Документация
https://symfony.com/doc/current/security.html

Если при установку возникает ошибка тогда надо обновить **symfony/flex** командой:
```
composer update symfony/flex --no-plugins --no-scripts
```

## 2. Настройка
Генерация пары ключей:
```
php bin/console lexik:jwt:generate-keypair
```
После генерации пары создаются два ключа в директории **config/jwt/**, приватный
и публичный. Ключи необходимы для подписи токенов.

Указываем где будем хранить пользователей и как их доставать.
```yaml
# config/security.yaml
security:
  providers:
    users:
      entity:
        class: App\Entity\User
        property: email
```
Провайдер находит entity User по идентификатору email.

Все классы которые имплементируют **PasswordAuthenticatedUserInterface** будет
использоваться автоалгоритм хеширования паролей.
```yaml
security:
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
```

Настраиваем firewall'ы. Firewall это правило, которое регламентирует доступ
чего-либо к чему-либо по шаблону.
```yaml
security:
  firewalls:
    dev:
      pattern: ^/(_(profiler|wdt)|css|images|js)/
      security: false
    login:
      pattern: ^/api/v1/auth/login
      stateless: true
      json_login:
        check_path: /api/v1/auth/login
        success_handler:
        failure_handler:
    api:
      pattern: ^/api
      stateless: true
      jwt: ~
```

Access control настраивает доступ для ролей:
```yaml
security:
  access_control:
    - { path: ^/api/v1/user, roles: IS_AUTHENTICATED_FULLY }
    - { path: ^/api, roles: PUBLIC_ACCESS }
```

Также необходимо добавить route:
```yaml
# config/routes.yaml

api_login_check:
  path: /api/v1/auth/login
```


## 3. Добавление custom данных в токен
doc: https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/2-data-customization.rst

Создаём listener:
```php
class JwtCreatedListener
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();
        $payload['id'] = $user->getUserIdentifier();

        $event->setData($payload);
    }
}
```
Добавляем прослушивание события:
```yaml
# config/services.yaml
services:
  App\Listener\JwtCreatedListener:
    tags:
      - { name: kernel.event_listener, event: lexik_jwt_authentication.on_jwt_created }
```

## 4. Установка refresh-token
```
composer require gesdinet/jwt-refresh-token-bundle
```
Настроить route:
```yaml
# config/routes.yaml

# ...

api_refresh_token:
  path: /api/auth/refresh
```

Настраиваем firewall:
```yaml
# config/packages/security.yaml

security:
  firewalls:
    api:
      entry_point: jwt
      refresh_jwt:
        check_path: /api/auth/refresh
```
entry_point: указывает что делать если пришел не авторизованный пользователь.

Донастраиваем пакет refresh_token:
```yaml
# config/packages/gesdinet_jwt_refresh_token.yaml

gesdinet_jwt_refresh_token:
  refresh_token_class: App\Entity\RefreshToken
  single_use: true
```
**single_use**: эта опция сперва удаляет из БД текущий токен, а потом вставляет
новый.

**Очистка refresh token**
```
php bin/console gesdinet:jwt:clear
```
