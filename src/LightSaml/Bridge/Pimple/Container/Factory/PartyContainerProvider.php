<?php

namespace LightSaml\Bridge\Pimple\Container\Factory;

use LightSaml\Bridge\Pimple\Container\PartyContainer;
use LightSaml\Error\LightSamlBuildException;
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
            ;
        };
    }
}
