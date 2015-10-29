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
