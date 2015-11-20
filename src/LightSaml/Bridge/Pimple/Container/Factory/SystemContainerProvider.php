<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Bridge\Pimple\Container\Factory;

use LightSaml\Bridge\Pimple\Container\SystemContainer;
use LightSaml\Provider\TimeProvider\SystemTimeProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SystemContainerProvider implements ServiceProviderInterface
{
    /** @var bool */
    private $mockSession;

    public function __construct($mockSession = false)
    {
        $this->mockSession = true;
    }

    /**
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple[SystemContainer::REQUEST] = function () {
            return Request::createFromGlobals();
        };

        $pimple[SystemContainer::SESSION] = function () {
            if ($this->mockSession) {
                $session = new Session(new MockArraySessionStorage());
            } else {
                $session = new Session();
            }
            $session->setName(sprintf('SID%s', mt_rand(1000, 9999)));
            $session->start();

            return $session;
        };

        $pimple[SystemContainer::TIME_PROVIDER] = function () {
            return new SystemTimeProvider();
        };

        $pimple[SystemContainer::EVENT_DISPATCHER] = function () {
            return new EventDispatcher();
        };

        $pimple[SystemContainer::LOGGER] = function () {
            return new NullLogger();
        };
    }
}
