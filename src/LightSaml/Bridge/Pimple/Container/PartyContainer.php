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

use LightSaml\Build\Container\PartyContainerInterface;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use LightSaml\Store\TrustOptions\TrustOptionsStoreInterface;

class PartyContainer extends AbstractPimpleContainer implements PartyContainerInterface
{
    const IDP_ENTITY_DESCRIPTOR = 'lightsaml.container.idp_entity_descriptor';
    const SP_ENTITY_DESCRIPTOR = 'lightsaml.container.sp_entity_descriptor';
    const TRUST_OPTIONS_STORE = 'lightsaml.container.trust_options_store';

    /**
     * @return EntityDescriptorStoreInterface
     */
    public function getIdpEntityDescriptorStore()
    {
        return $this->pimple[self::IDP_ENTITY_DESCRIPTOR];
    }

    /**
     * @return EntityDescriptorStoreInterface
     */
    public function getSpEntityDescriptorStore()
    {
        return $this->pimple[self::SP_ENTITY_DESCRIPTOR];
    }

    /**
     * @return TrustOptionsStoreInterface
     */
    public function getTrustOptionsStore()
    {
        return $this->pimple[self::TRUST_OPTIONS_STORE];
    }
}
