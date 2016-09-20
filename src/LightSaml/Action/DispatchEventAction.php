<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action;

use LightSaml\Context\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class DispatchEventAction implements ActionInterface
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var string */
    protected $event;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $event
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, $event)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->event = $event;
    }

    /**
     * @param ContextInterface $context
     *
     * @return void
     */
    public function execute(ContextInterface $context)
    {
        $this->eventDispatcher->dispatch($this->event, new GenericEvent($context));
    }
}
