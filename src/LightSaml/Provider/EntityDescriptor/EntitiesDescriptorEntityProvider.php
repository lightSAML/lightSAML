<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Provider\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Provider\EntitiesDescriptor\EntitiesDescriptorProviderInterface;

class EntitiesDescriptorEntityProvider implements EntityDescriptorProviderInterface
{
    /** @var EntitiesDescriptorProviderInterface */
    private $entitiesDescriptorProvider;

    /** @var string */
    private $entityId;

    /** @var EntityDescriptor */
    private $entityDescriptor;

    /**
     * @param EntitiesDescriptorProviderInterface $entitiesDescriptorProvider
     * @param string                              $entityId
     */
    public function __construct(EntitiesDescriptorProviderInterface $entitiesDescriptorProvider, $entityId)
    {
        $this->entitiesDescriptorProvider = $entitiesDescriptorProvider;
        $this->entityId = $entityId;
    }

    /**
     * @return EntityDescriptor
     */
    public function get()
    {
        if (null == $this->entityDescriptor) {
            $this->entityDescriptor = $this->entitiesDescriptorProvider->get()->getByEntityId($this->entityId);
        }

        return $this->entityDescriptor;
    }
}
