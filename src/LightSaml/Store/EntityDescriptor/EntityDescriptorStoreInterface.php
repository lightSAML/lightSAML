<?php

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
