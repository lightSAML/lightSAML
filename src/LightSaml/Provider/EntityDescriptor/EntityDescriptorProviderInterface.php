<?php

namespace LightSaml\Provider\EntityDescriptor;

use LightSaml\Model\Metadata\EntityDescriptor;

interface EntityDescriptorProviderInterface
{
    /**
     * @return EntityDescriptor
     */
    public function get();
}
