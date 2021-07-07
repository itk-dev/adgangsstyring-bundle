<?php


namespace ItkDev\AdgangsstyringBundle\EventSubscriber;

use ItkDev\Adgangsstyring\Event\CommitEvent;
use ItkDev\Adgangsstyring\Event\StartEvent;
use ItkDev\Adgangsstyring\Event\UserDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            StartEvent::class => 'start',
            UserDataEvent::class => 'userData',
            CommitEvent::class => 'commit',
        ];
    }

    /**
     * Start
     */
    public function start(StartEvent $event)
    {

    }

    /**
     * Handle user data
     */
    public function userDate(UserDataEvent $event)
    {

    }

    /**
     * Commit
     */
    public function commit(CommitEvent $event)
    {

    }
}