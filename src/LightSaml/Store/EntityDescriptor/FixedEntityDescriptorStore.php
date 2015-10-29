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

use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;

class FixedEntityDescriptorStore implements EntityDescriptorStoreInterface
{
    /** @var array|EntityDescriptor[] entityId=>descriptor */
    protected $descriptors = array();

    /**
     * @param EntityDescriptor|EntitiesDescriptor $entityDescriptor
     *
     * @return FixedEntityDescriptorStore
     *
     * @throws \InvalidArgumentException
     */
    public function add($entityDescriptor)
    {
        if ($entityDescriptor instanceof EntityDescriptor) {
            if (false == $entityDescriptor->getEntityID()) {
                throw new \InvalidArgumentException('EntityDescriptor must have entityId set');
            }
            $this->descriptors[$entityDescriptor->getEntityID()] = $entityDescriptor;
        } elseif ($entityDescriptor instanceof EntitiesDescriptor) {
            foreach ($entityDescriptor->getAllItems() as $item) {
                $this->add($item);
            }
        } else {
            throw new \InvalidArgumentException('Expected EntityDescriptor or EntitiesDescriptor');
        }

        return $this;
    }

    /**
     * @param string $entityId
     *
     * @return EntityDescriptor|null
     */
    public function get($entityId)
    {
        if (isset($this->descriptors[$entityId])) {
            return $this->descriptors[$entityId];
        }

        return null;
    }

    /**
     * @param string $entityId
     *
     * @return bool
     */
    public function has($entityId)
    {
        return isset($this->descriptors[$entityId]);
    }

    /**
     * @return array|EntityDescriptor[]
     */
    public function all()
    {
        return array_values($this->descriptors);
    }
}
