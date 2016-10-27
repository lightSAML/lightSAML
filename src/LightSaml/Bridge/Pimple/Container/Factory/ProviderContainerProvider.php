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

use LightSaml\Bridge\Pimple\Container\ProviderContainer;
use LightSaml\Error\LightSamlBuildException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ProviderContainerProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple[ProviderContainer::ATTRIBUTE_VALUE_PROVIDER] = function () {
            throw new LightSamlBuildException('Attribute value provider not set');
        };

        $pimple[ProviderContainer::SESSION_INFO_PROVIDER] = function () {
            throw new LightSamlBuildException('Session info provider not set');
        };

        $pimple[ProviderContainer::NAME_ID_PROVIDER] = function () {
            throw new LightSamlBuildException('Name ID provider not set');
        };
    }
}
