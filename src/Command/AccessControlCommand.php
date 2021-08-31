<?php

namespace ItkDev\AzureAdDeltaSyncBundle\Command;

use ItkDev\AzureAdDeltaSync\Controller;
use ItkDev\AzureAdDeltaSyncBundle\Handler\UserHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AccessControlCommand extends Command
{
    private $controller;
    private $handler;
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'delta-sync:run';

    public function __construct(Controller $controller, UserHandler $handler, string $name = null)
    {
        $this->controller = $controller;
        $this->handler = $handler;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Starts access control flow');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->controller->run($this->handler);

        return Command::SUCCESS;
    }
}
