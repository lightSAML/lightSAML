<?php

namespace LightSaml\Provider\EntitiesDescriptor;

use LightSaml\Model\Metadata\EntitiesDescriptor;

interface EntitiesDescriptorProviderInterface
{
    /**
     * @return EntitiesDescriptor
     */
    public function get();
}
