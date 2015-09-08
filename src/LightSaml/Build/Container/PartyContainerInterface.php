<?php

namespace LightSaml\Build\Container;

use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use LightSaml\Store\TrustOptions\TrustOptionsStoreInterface;

interface PartyContainerInterface
{
    /**
     * @return EntityDescriptorStoreInterface
     */
    public function getIdpEntityDescriptorStore();

    /**
     * @return EntityDescriptorStoreInterface
     */
    public function getSpEntityDescriptorStore();

    /**
     * @return TrustOptionsStoreInterface
     */
    public function getTrustOptionsStore();
}
