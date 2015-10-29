<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
