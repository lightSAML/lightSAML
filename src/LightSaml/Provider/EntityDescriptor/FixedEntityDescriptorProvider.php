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

class FixedEntityDescriptorProvider implements EntityDescriptorProviderInterface
{
    /** @var EntityDescriptor */
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
