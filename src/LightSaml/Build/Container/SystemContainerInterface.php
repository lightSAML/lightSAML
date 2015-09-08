<?php

namespace LightSaml\Build\Container;

use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface SystemContainerInterface
{
    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @return SessionInterface
     */
    public function getSession();

    /**
     * @return TimeProviderInterface
     */
    public function getTimeProvider();

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher();

    /**
     * @return LoggerInterface
     */
    public function getLogger();
}
