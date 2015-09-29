<?php

namespace LightSaml\Provider\EntityDescriptor;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Metadata\EntityDescriptor;

class FileEntityDescriptorProvider implements EntityDescriptorProviderInterface
{
    /** @var string */
    private $filename;

    private $entityDescriptor;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return EntityDescriptor
     */
    public function get()
    {
        if (null == $this->entityDescriptor) {
            $this->entityDescriptor = new EntityDescriptor();
            $deserializationContext = new DeserializationContext();
            $this->entityDescriptor->deserialize($deserializationContext->getDocument()->firstChild, $deserializationContext);
        }

        return $this->entityDescriptor;
    }
}
