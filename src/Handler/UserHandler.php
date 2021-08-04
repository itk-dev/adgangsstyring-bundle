<?php

namespace ItkDev\AdgangsstyringBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use ItkDev\Adgangsstyring\Handler\HandlerInterface;
use ItkDev\AdgangsstyringBundle\Event\DeleteUserEvent;
use ItkDev\AdgangsstyringBundle\Exception\UserClaimException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UserHandler implements HandlerInterface
{

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var string
     */
    private $className;
    /**
     * @var string
     */
    private $username;
    /**
     * @var FilesystemAdapter
     */
    private $cache;
    /**
     * @var string
     */
    private $group_user_property;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $em, string $className, string $username, string $group_user_property)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->className = $className;
        $this->username = $username;
        $this->group_user_property = $group_user_property;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function start(): void
    {
        // Get all users in system
        $repository = $this->em->getRepository($this->className);
        $users = $repository->findAll();

        // Create PropertyAccessor
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        // setup cache
        $this->cache = new FilesystemAdapter();
        $systemUsers = $this->cache->getItem('adgangsstyring.system_users');

        // Array for users marked for potential removal
        $systemUsersArray = [];

        foreach ($users as $user) {
            $userData = $propertyAccessor->getValue($user, $this->username);
            // Add to potential removal array
            array_push($systemUsersArray, $userData);
        }

        // Save array of users marked for removal
        $systemUsers->set($systemUsersArray);
        $this->cache->save($systemUsers);
    }

    /**
     * @throws UserClaimException
     * @throws InvalidArgumentException
     */
    public function retainUsers(array $users): void
    {
        // Get array users in system
        $systemUsers = $this->cache->getItem('adgangsstyring.system_users');
        $systemUsersArray = $systemUsers->get();

        // Run through users in group and delete from system users array
        foreach ($users as $user) {
            if (!isset($user[$this->group_user_property])) {
                $message = sprintf('User claim: %s, does not exist.', $this->group_user_property);
                throw new UserClaimException($message);
            }
            $value = $user[$this->group_user_property];

            if (($key = array_search($value, $systemUsersArray)) !== false) {
                unset($systemUsersArray[$key]);
            }
        }

        // Save (modified) array of users marked for removal
        $systemUsers->set($systemUsersArray);
        $this->cache->save($systemUsers);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function commit(): void
    {
        // Get array users in system whom remain
        $systemUsers = $this->cache->getItem('adgangsstyring.system_users');
        $systemUsersArray = $systemUsers->get();

        // Dispatch new event with remaining list
        $accessControlEvent = new DeleteUserEvent($systemUsersArray);

        $this->dispatcher->dispatch($accessControlEvent);
    }
}
