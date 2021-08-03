<?php

namespace ItkDev\AdgangsstyringBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DeleteUserEvent extends Event
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
}
