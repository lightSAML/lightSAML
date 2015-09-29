<?php

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
