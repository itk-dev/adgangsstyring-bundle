<?php


namespace ItkDev\AdgangsstyringBundle\Command;

use ItkDev\Adgangsstyring\Controller;
use ItkDev\AdgangsstyringBundle\EventSubscriber\EventSubscriber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AccessControlCommand extends Command
{

    private $options;
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'adgangsstyring:run';

    public function __construct(array $options, string $name = null)
    {
        $this->options = $options;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Starts access control flow');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $subscriber = new EventSubscriber();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($subscriber);
        $controller = new Controller($eventDispatcher, $this->options);

        $controller->run();
        return Command::SUCCESS;
    }
}