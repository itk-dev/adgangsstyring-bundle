<?php

namespace ItkDev\AzureAdDeltaSyncBundle\Command;

use ItkDev\AzureAdDeltaSync\Controller;
use ItkDev\AzureAdDeltaSync\Exception\DataException;
use ItkDev\AzureAdDeltaSync\Exception\NetworkException;
use ItkDev\AzureAdDeltaSync\Exception\TokenException;
use ItkDev\AzureAdDeltaSyncBundle\Handler\UserHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AccessControlCommand extends Command
{
    private Controller $controller;
    private UserHandler $handler;
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'delta-sync:run';

    /**
     * AccessControlCommand constructor.
     *
     * @param Controller $controller
     * @param UserHandler $handler
     * @param string|null $name
     */
    public function __construct(Controller $controller, UserHandler $handler, string $name = null)
    {
        $this->controller = $controller;
        $this->handler = $handler;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Starts the Azure AD Delta Sync flow');
    }

    /**
     * Executes the Azure AD Delta Sync flow
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws DataException
     * @throws NetworkException
     * @throws TokenException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->controller->run($this->handler);
        } catch (DataException | NetworkException | TokenException $e) {
            throw $e;
        }

        return Command::SUCCESS;
    }
}
