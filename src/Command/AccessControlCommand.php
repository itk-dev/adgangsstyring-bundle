<?php

namespace ItkDev\AdgangsstyringBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use ItkDev\Adgangsstyring\Controller;
use ItkDev\AdgangsstyringBundle\Handler\UserHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AccessControlCommand extends Command
{
    private $dispatcher;
    private $options;
    private $em;
    private $userClass;
    private $username;
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'adgangsstyring:run';

    public function __construct(EventDispatcherInterface $dispatcher, array $options, EntityManagerInterface $em, string $userClass, string $username, string $name = null)
    {
        $this->dispatcher = $dispatcher;
        $this->options = $options;
        $this->em = $em;
        $this->userClass = $userClass;
        $this->username = $username;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Starts access control flow');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $handler = new UserHandler($this->dispatcher, $this->em, $this->userClass, $this->username);

        $client = new Client();
        $controller = new Controller($client, $this->options);

        $controller->run($handler);

        return Command::SUCCESS;
    }
}
