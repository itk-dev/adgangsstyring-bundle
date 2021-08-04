# Adgangsstyring Bundle

Symfony bundle for user access control via Azure AD.

## Installation

To install run

```shell
composer require itkdev/adgangsstyring-bundle
```

If you wish to run the coding standard tests for Markdown files

```shell
yarn install
```

## Usage

Before being able to use the bundle, you must have
your own `User` entity, `UserRepository`  and database setup.

You will need to configure variables for
Microsoft groups, and the above mentioned `User` entity:

### Variable configuration

In `/config/packages` you need the following `itkdev_adgangsstyring.yaml` file:

```yaml
itkdev_adgangsstyring:
  adgangsstyring_options:
    tenant_id: 'some_tenant_id'
    client_id: 'some_client_id'
    client_secret: 'some_client_secret'
    group_id: 'some_group_id'
  user_options:
    user_class: 'App\Entity\User'
    user_property: 'some_user_property'
    user_claim_property: 'some_user_claim_property'
```

Note that `user_property` and `user_claim_property`
should be unique properties and needs to match up.

### Listening to DeleteUserEvent

The bundle dispatches a `DeleteUserEvent` containing
a list of users for potential removal. This is a list of users
whom are registered in the using system, but are not assigned
to the AD group. This means the bundle does not exclude users
having specific characteristics, i.e. super admin users or
akin may be among the users the bundle provides.

Therefore, the using system will need to implement an EventListener
or EventSubscriber that listens to the `DeleteUserEvent`.

#### Example EventSubscriber

```php
<?php

namespace App\EventSubscriber;

use ItkDev\AdgangsstyringBundle\Event\DeleteUserEvent;
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
php bin/console adgangsstyring:run
```

It is up to using system to decide how and when to run
this command.

## Development Setup

### Unit Testing

TO BE MADE

### Check Coding Standard

* PHP files (PHP_CodeSniffer)

    ```shell
    composer check-coding-standards
    ```

* Markdown files (markdownlint standard rules)

    ```shell
    yarn install
    yarn check-coding-standards
    ```

### Apply Coding Standards

* PHP files (PHP_CodeSniffer)

    ```shell
    composer apply-coding-standards
    ```

* Markdown files (markdownlint standard rules)

    ```shell
    yarn install
    yarn apply-coding-standards
    ```

## Versioning

We use [SemVer](http://semver.org/) for versioning.
For the versions available, see the
[tags on this repository](https://github.com/itk-dev/adgangsstyring-bundle/tags).

## License

This project is licensed under the MIT License - see the
[LICENSE.md](LICENSE.md) file for details
