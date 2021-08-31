<?php

namespace ItkDev\AzureAdDeltaSyncBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use ItkDev\AzureAdDeltaSync\Handler\HandlerInterface;
use ItkDev\AzureAdDeltaSyncBundle\Event\DeleteUserEvent;
use ItkDev\AzureAdDeltaSyncBundle\Exception\UserClaimException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
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
    private $user_class;
    /**
     * @var string
     */
    private $user_property;
    /**
     * @var AdapterInterface
     */
    private $cache;
    /**
     * @var string
     */
    private $user_claim_property;

    public function __construct(AdapterInterface $cache, EventDispatcherInterface $dispatcher, EntityManagerInterface $em, string $user_class, string $user_property, string $user_claim_property)
    {
        $this->cache = $cache;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->user_class = $user_class;
        $this->user_property = $user_property;
        $this->user_claim_property = $user_claim_property;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function collectUsersForDeletionList(): void
    {
        // Get all users in system
        $repository = $this->em->getRepository($this->user_class);
        $users = $repository->findAll();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $systemUsers = $this->cache->getItem('azure_ad_delta_sync.system_users');

        // Array for users marked for potential removal
        $systemUsersArray = [];

        foreach ($users as $user) {
            $userData = $propertyAccessor->getValue($user, $this->user_property);
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
    public function removeUsersFromDeletionList(array $users): void
    {
        // Get array users in system
        $systemUsers = $this->cache->getItem('azure_ad_delta_sync.system_users');
        $systemUsersArray = $systemUsers->get();

        // Run through users in group and delete from system users array
        foreach ($users as $user) {
            if (!isset($user[$this->user_claim_property])) {
                $message = sprintf('User claim: %s, does not exist.', $this->user_claim_property);
                throw new UserClaimException($message);
            }
            $value = $user[$this->user_claim_property];

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
    public function commitDeletionList(): void
    {
        // Get array users in system whom remain
        $systemUsers = $this->cache->getItem('azure_ad_delta_sync.system_users');
        $systemUsersArray = $systemUsers->get();

        // Dispatch new event with remaining list
        $accessControlEvent = new DeleteUserEvent($systemUsersArray);

        $this->dispatcher->dispatch($accessControlEvent);
    }
}
