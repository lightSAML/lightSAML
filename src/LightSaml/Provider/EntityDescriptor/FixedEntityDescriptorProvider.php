<?php

namespace LightSaml\Provider\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;

class FixedEntityDescriptorProvider implements EntityDescriptorProviderInterface
{
    /** @var  EntityDescriptor */
    protected $entityDescriptor;

    /**
     * @param EntityDescriptor $entityDescriptor
     */
    public function __construct(EntityDescriptor $entityDescriptor)
    {
        $this->entityDescriptor = $entityDescriptor;
    }

    /**
     * @return EntityDescriptor
     */
    public function get()
    {
        return $this->entityDescriptor;
    }
}
