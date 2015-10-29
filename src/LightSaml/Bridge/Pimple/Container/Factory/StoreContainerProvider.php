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

use LightSaml\Bridge\Pimple\Container\StoreContainer;
use LightSaml\Build\Container\SystemContainerInterface;
use LightSaml\Store\Id\NullIdStore;
use LightSaml\Store\Request\RequestStateSessionStore;
use LightSaml\Store\Sso\SsoStateSessionStore;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class StoreContainerProvider implements ServiceProviderInterface
{
    /** @var SystemContainerInterface */
    private $systemContainer;

    public function __construct(SystemContainerInterface $systemContainer)
    {
        $this->systemContainer = $systemContainer;
    }

    /**
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple[StoreContainer::REQUEST_STATE_STORE] = function () {
            return new RequestStateSessionStore($this->systemContainer->getSession(), 'main');
        };

        $pimple[StoreContainer::ID_STATE_STORE] = function () {
            return new NullIdStore();
        };

        $pimple[StoreContainer::SSO_STATE_STORE] = function () {
            return new SsoStateSessionStore($this->systemContainer->getSession(), 'samlsso');
        };
    }
}
