<?php

namespace LightSaml\Action\Profile;

use LightSaml\Context\Profile\ProfileContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class DispatchEventAction extends AbstractProfileAction
{
    /** @var  EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var  string */
    protected $event;

    /**
     * @param LoggerInterface          $logger
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $event
     */
    public function __construct(LoggerInterface $logger, EventDispatcherInterface $eventDispatcher, $event)
    {
        parent::__construct($logger);

        $this->eventDispatcher = $eventDispatcher;
        $this->event = $event;
    }

    protected function doExecute(ProfileContext $context)
    {
        $this->eventDispatcher->dispatch($this->event, new GenericEvent($context));
    }
}
