<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Bridge\Pimple\Container;

use LightSaml\Build\Container\SystemContainerInterface;
use LightSaml\Provider\TimeProvider\TimeProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SystemContainer extends AbstractPimpleContainer implements SystemContainerInterface
{
    const REQUEST = 'lightsaml.container.request';
    const SESSION = 'lightsaml.container.session';
    const TIME_PROVIDER = 'lightsaml.container.time_provider';
    const EVENT_DISPATCHER = 'lightsaml.container.event_dispatcher';
    const LOGGER = 'lightsaml.container.logger';

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->pimple[self::REQUEST];
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->pimple[self::SESSION];
    }

    /**
     * @return TimeProviderInterface
     */
    public function getTimeProvider()
    {
        return $this->pimple[self::TIME_PROVIDER];
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->pimple[self::EVENT_DISPATCHER];
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->pimple[self::LOGGER];
    }
}
