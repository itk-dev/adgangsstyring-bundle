<?php

namespace ItkDev\AzureAdDeltaSyncBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DeleteUserEvent extends Event
{
    private array $data;

    /**
     * DeleteUserEvent constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Gets data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets data
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
