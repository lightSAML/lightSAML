<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Store\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;

interface EntityDescriptorStoreInterface
{
    /**
     * @param string $entityId
     *
     * @return EntityDescriptor|null
     */
    public function get($entityId);

    /**
     * @param string $entityId
     *
     * @return bool
     */
    public function has($entityId);

    /**
     * @return array|EntityDescriptor[]
     */
    public function all();
}
