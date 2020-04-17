<?php

namespace LightSaml\Event;

use Symfony\Contracts\EventDispatcher\Event;

class BindingMessageReceived extends Event
{
    public const NAME = 'lightsaml.binding_message_received';

    protected $messageString;

    public function __construct(string $messageString)
    {
        $this->messageString = $messageString;
    }

    public function getMessageString() : string
    {
        return $this->messageString;
    }
}