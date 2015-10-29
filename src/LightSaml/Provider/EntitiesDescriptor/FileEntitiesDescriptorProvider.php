<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Provider\EntitiesDescriptor;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Metadata\EntitiesDescriptor;

class FileEntitiesDescriptorProvider implements EntitiesDescriptorProviderInterface
{
    /** @var string */
    private $filename;

    /** @var EntitiesDescriptor */
    private $entitiesDescriptor;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return EntitiesDescriptor
     */
    public function get()
    {
        if (null == $this->entitiesDescriptor) {
            $this->entitiesDescriptor = new EntitiesDescriptor();
            $deserializationContext = new DeserializationContext();
            $deserializationContext->getDocument()->load($this->filename);
            $this->entitiesDescriptor->deserialize($deserializationContext->getDocument()->firstChild, $deserializationContext);
        }

        return $this->entitiesDescriptor;
    }
}
