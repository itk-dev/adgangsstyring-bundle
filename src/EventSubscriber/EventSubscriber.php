<?php


namespace ItkDev\AdgangsstyringBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use ItkDev\Adgangsstyring\Event\CommitEvent;
use ItkDev\Adgangsstyring\Event\StartEvent;
use ItkDev\Adgangsstyring\Event\UserDataEvent;
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
        $repository = $this->em->getRepository($this->className);

        $users = $repository->findAll();

        var_dump($this->username);
        // Somehow mark all users in system for deletion
        //var_dump('starterEvent');
    }

    /**
     * Handle user data
     */
    public function userData(UserDataEvent $event)
    {
        // Get list of users in group from $event
        // Run through list and compare to list created in start
        // If theres a match remove deletion mark from start list
        //var_dump('userDataEvent');
    }

    /**
     * Commit
     */
    public function commit(CommitEvent $event)
    {
        // Send event containing list of users still with deletion mark
        //var_dump('commitEvent');
    }
}