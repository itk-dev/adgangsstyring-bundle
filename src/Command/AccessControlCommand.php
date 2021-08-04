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
    private $user_class;
    private $user_property;
    private $user_claim_property;
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'adgangsstyring:run';

    public function __construct(EventDispatcherInterface $dispatcher, array $options, EntityManagerInterface $em, string $user_class, string $user_property, string $user_claim_property, string $name = null)
    {
        $this->dispatcher = $dispatcher;
        $this->options = $options;
        $this->em = $em;
        $this->user_class = $user_class;
        $this->user_property = $user_property;
        $this->user_claim_property = $user_claim_property;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Starts access control flow');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $handler = new UserHandler($this->dispatcher, $this->em, $this->user_class, $this->user_property, $this->user_claim_property);

        $client = new Client();
        $controller = new Controller($client, $this->options);

        $controller->run($handler);

        return Command::SUCCESS;
    }
}
