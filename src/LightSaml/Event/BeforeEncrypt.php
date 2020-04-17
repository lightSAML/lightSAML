<?php

namespace LightSaml\Event;

use LightSaml\Context\ContextInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeEncrypt extends Event
{
    public const NAME = 'lightsaml.before_encrypt';

    protected $context;

    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    public function getContext() : ContextInterface
    {
        return $this->context;
    }
}