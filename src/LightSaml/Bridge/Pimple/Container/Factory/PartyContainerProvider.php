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

use LightSaml\Bridge\Pimple\Container\PartyContainer;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Store\EntityDescriptor\FixedEntityDescriptorStore;
use LightSaml\Store\TrustOptions\FixedTrustOptionsStore;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PartyContainerProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple[PartyContainer::IDP_ENTITY_DESCRIPTOR] = function () {
            return new FixedEntityDescriptorStore();
        };

        $pimple[PartyContainer::SP_ENTITY_DESCRIPTOR] = function () {
            return new FixedEntityDescriptorStore();
        };

        $pimple[PartyContainer::TRUST_OPTIONS_STORE] = function () {
            return new FixedTrustOptionsStore(new TrustOptions());
        };
    }
}
