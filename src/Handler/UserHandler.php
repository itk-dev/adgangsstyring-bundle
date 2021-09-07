<?php

namespace ItkDev\AzureAdDeltaSyncBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use ItkDev\AzureAdDeltaSync\Handler\HandlerInterface;
use ItkDev\AzureAdDeltaSyncBundle\Event\DeleteUserEvent;
use ItkDev\AzureAdDeltaSyncBundle\Exception\AzureUserException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UserHandler implements HandlerInterface
{
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $em;
    private string $system_user_class;
    private string $system_user_property;
    private AdapterInterface $cache;
    private string $azure_ad_user_property;

    /**
     * UserHandler constructor.
     *
     * @param AdapterInterface $cache
     *   Cache adapter for caching users
     * @param EventDispatcherInterface $dispatcher
     *   Event Dispatcher for dispatching events
     * @param EntityManagerInterface $em
     *   Entity Manager for collecting system users
     * @param string $system_user_class
     *   System User class
     * @param string $system_user_property
     *   System unique user property
     * @param string $azure_ad_user_property
     *   Azure AD User property
     */
    public function __construct(AdapterInterface $cache, EventDispatcherInterface $dispatcher, EntityManagerInterface $em, string $system_user_class, string $system_user_property, string $azure_ad_user_property)
    {
        $this->cache = $cache;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->system_user_class = $system_user_class;
        $this->system_user_property = $system_user_property;
        $this->azure_ad_user_property = $azure_ad_user_property;
    }

    /**
     * Collects users for deletion.
     *
     * @throws InvalidArgumentException
     */
    public function collectUsersForDeletionList(): void
    {
        $repository = $this->em->getRepository($this->system_user_class);
        $users = $repository->findAll();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        // Cache for users marked for removal
        $deletionList = $this->cache->getItem('azure_ad_delta_sync.deletion_list');

        // Array for users marked for potential removal
        $systemUsersArray = [];

        foreach ($users as $user) {
            $userData = $propertyAccessor->getValue($user, $this->system_user_property);
            // Add to potential removal array
            array_push($systemUsersArray, $userData);
        }

        // Save array of users marked for removal
        $deletionList->set($systemUsersArray);
        $this->cache->save($deletionList);
    }

    /**
     * Removes users from deletion list.
     *
     * @throws AzureUserException
     * @throws InvalidArgumentException
     */
    public function removeUsersFromDeletionList(array $users): void
    {
        $deletionListItem = $this->cache->getItem('azure_ad_delta_sync.deletion_list');
        $deletionList = $deletionListItem->get();

        $azureUsers = [];

        // Run through users in group and retrieve azure_ad_user_property
        foreach ($users as $user) {
            if (!isset($user[$this->azure_ad_user_property])) {
                $message = sprintf('User property: %s, does not exist.', $this->azure_ad_user_property);
                throw new AzureUserException($message);
            }

            array_push($azureUsers, $user[$this->azure_ad_user_property]);
        }

        $intersectingUsers = array_intersect($azureUsers, $deletionList);
        $modifiedDeletionList = array_diff($deletionList, $intersectingUsers);

        // Save modified array of users marked for removal
        $deletionListItem->set($modifiedDeletionList);
        $this->cache->save($deletionListItem);
    }

    /**
     * Dispatches DeleteUserEvent containing deletion list.
     *
     * @throws InvalidArgumentException
     */
    public function commitDeletionList(): void
    {
        $deletionListItem = $this->cache->getItem('azure_ad_delta_sync.deletion_list');
        $deletionList = $deletionListItem->get();

        $event = $this->createDeleteUserEvent($deletionList);

        $this->dispatcher->dispatch($event);
    }

    public function createDeleteUserEvent($deletionList): DeleteUserEvent
    {
        return new DeleteUserEvent($deletionList);
    }
}
