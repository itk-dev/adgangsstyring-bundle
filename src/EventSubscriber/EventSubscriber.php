<?php


namespace ItkDev\AdgangsstyringBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use ItkDev\Adgangsstyring\Event\CommitEvent;
use ItkDev\Adgangsstyring\Event\StartEvent;
use ItkDev\Adgangsstyring\Event\UserDataEvent;
use ItkDev\AdgangsstyringBundle\Event\AccessControlEvent;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
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

    private $cache;

    public function __construct(EntityManagerInterface $em, string $className, string $username)
    {
        $this->em = $em;
        $this->className = $className;
        $this->username = $username;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StartEvent::class => ['start'],
            UserDataEvent::class => ['userData'],
            CommitEvent::class => ['commit'],
        ];
    }

    /**
     * Start
     */
    public function start(StartEvent $event)
    {
        // Get all users in system
        $repository = $this->em->getRepository($this->className);
        $users = $repository->findAll();

        // Compute getter method
        $nameOfGetter = 'get'.ucfirst($this->username);

        // setup cache
        $this->cache = new FilesystemAdapter();
        $systemUsers = $this->cache->getItem('adgangsstyring.system_users');

        // Array for users marked for potential removal
        $systemUsersArray = [];

        foreach ($users as $user){

            // Ensure getter exists
            if (!method_exists($user, $nameOfGetter)) {
                $message = sprintf('Getter %s not found in %s.', $nameOfGetter, get_class($user));
                throw new \Exception($message);
            }

            // Call getter
            $userData = call_user_func([
                $user,
                $nameOfGetter
            ]);

            // Add to removal array
            array_push($systemUsersArray, $userData);
        }
        var_dump($systemUsersArray);

        // Save array of users marked for removal
        $systemUsers->set($systemUsersArray);
        $this->cache->save($systemUsers);
    }

    /**
     * Handle user data
     */
    public function userData(UserDataEvent $event)
    {
        // Get array users in system
        $systemUsers = $this->cache->getItem('adgangsstyring.system_users');
        $systemUsersArray = $systemUsers->get();

        // Run through users in group and delete from system users array
        foreach ($event->getData() as $user){
            $value = $user['userPrincipalName'];

            if (($key = array_search($value, $systemUsersArray)) !== false) {
                unset($systemUsersArray[$key]);
            }
        }

        // Save (modified) array of users marked for removal
        $systemUsers->set($systemUsersArray);
        $this->cache->save($systemUsers);
    }

    /**
     * Commit
     */
    public function commit(CommitEvent $event)
    {
        // Get array users in system whom remain
        $systemUsers = $this->cache->getItem('adgangsstyring.system_users');
        $systemUsersArray = $systemUsers->get();

        // Dispatch new event with remaining list
        $dispatcher = new EventDispatcher();
        $accessControlEvent = new AccessControlEvent($systemUsersArray);

        $dispatcher->dispatch($accessControlEvent);

        var_dump($systemUsersArray);
    }
}