# Azure AD Delta Sync Symfony Bundle

Symfony bundle for Azure AD Delta Sync flow.

## Installation

To install run

```shell
composer require itk-dev/azure-ad-delta-sync-bundle
```

## Usage

Before being able to use the bundle, you must have
your own `User` entity, `UserRepository`  and database setup.

You will need to configure variables for
Microsoft groups, the above mentioned `User` entity
and cache pool:

### Variable configuration

In `/config/packages` you need the following `itkdev_azure_ad_delta_sync.yaml` file:

```yaml
itkdev_azure_ad_delta_sync:
  azure_ad_delta_sync_options:
    tenant_id: 'some_tenant_id'
    client_id: 'some_client_id'
    client_secret: 'some_client_secret'
    group_id: 'some_group_id'
  user_options:
    system_user_class: 'App\Entity\User'
    system_user_property: 'some_user_property'
    azure_ad_user_property: 'some_azure_ad_user_property'
  cache_options:
    cache_pool: 'cache.app'
```

Here the `azure_ad_user_property` should be a property on the
Azure AD user that is equivalent to the `system_user_property`,
as this is how we compare system users with Microsoft group users.
For this reason the comparing property must also be unique.

### Listening to DeleteUserEvent

The bundle dispatches a `DeleteUserEvent` containing
a list of user properties (`system_user_property`) for potential removal.
The using system should implement logic to ensure
these users are not deleted unintentionally.

Therefore, the using system will need to implement an EventListener
or EventSubscriber that listens to the `DeleteUserEvent`.

#### Example EventSubscriber

```php
<?php

namespace App\EventSubscriber;

use ItkDev\AzureAdDeltaSyncBundle\Event\DeleteUserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteUserEventSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            DeleteUserEvent::class => 'deleteUsers',
        ];
    }

    public function deleteUsers(DeleteUserEvent $event)
    {
        // User deletion logic here
    }
}
```

### Starting the flow

To start the flow the using system execute the follow CLI command:

```shell
php bin/console delta-sync:run
```

It is up to the using system to decide how and when to run
this command.

## Development Setup

A `docker-compose.yml` file with a PHP 7.4 image is included in this project.
To install the dependencies you can run

 ```shell
 docker compose up -d
 docker compose exec phpfpm composer install
 ```

### Unit Testing

We use PHPUnit for unit testing. To run the tests:

```shell
docker compose exec phpfpm composer install
docker compose exec phpfpm ./vendor/bin/phpunit tests
```

The test suite uses [Mocks](https://phpunit.de/manual/6.5/en/test-doubles.html)
for generation of test doubles.

### Check Coding Standard

* PHP files (PHP_CodeSniffer)

    ```shell
    docker compose exec phpfpm composer check-coding-standards
    ```

* Markdown files (markdownlint standard rules)

    ```shell
    docker run -v ${PWD}:/app itkdev/yarn:latest install
    docker run -v ${PWD}:/app itkdev/yarn:latest check-coding-standards
    ```

### GitHub Actions

All code checks mentioned above are automatically run by [GitHub
Actions](https://github.com/features/actions) when a pull request is created.

To run the actions locally, install [act](https://github.com/nektos/act) and run

```sh
act -P ubuntu-latest=shivammathur/node:focal pull_request
```

Use `act -P ubuntu-latest=shivammathur/node:focal pull_request --list` to see
individual workflow jobs that can be run, e.g.

```sh
act -P ubuntu-latest=shivammathur/node:focal pull_request --job phpcsfixer
```

### Apply Coding Standards

* PHP files (PHP_CodeSniffer)

    ```shell
    docker compose exec phpfpm composer apply-coding-standards
    ```

* Markdown files (markdownlint standard rules)

    ```shell
    docker run -v ${PWD}:/app itkdev/yarn:latest install
    docker run -v ${PWD}:/app itkdev/yarn:latest apply-coding-standards
    ```

## Versioning

We use [SemVer](http://semver.org/) for versioning.
For the versions available, see the
[tags on this repository](https://github.com/itk-dev/adgangsstyring-bundle/tags).

## License

This project is licensed under the MIT License - see the
[LICENSE.md](LICENSE.md) file for details
