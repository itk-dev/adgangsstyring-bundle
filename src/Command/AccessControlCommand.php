<?php


namespace ItkDev\AdgangsstyringBundle\Command;

use ItkDev\Adgangsstyring\Controller;
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
        $eventDispatcher = new EventDispatcher();
        $controller = new Controller($eventDispatcher, $this->options);
        return Command::SUCCESS;
    }
}