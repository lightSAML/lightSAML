<?php

namespace LightSaml\Provider\EntityDescriptor;

use LightSaml\Provider\EntitiesDescriptor\FileEntitiesDescriptorProvider;

class FileEntityDescriptorProviderFactory
{
    /**
     * @param string $filename
     *
     * @return FileEntityDescriptorProvider
     */
    public static function fromEntityDescriptorFile($filename)
    {
        return new FileEntityDescriptorProvider($filename);
    }

    /**
     * @param string $filename
     * @param string $entityId
     *
     * @return EntitiesDescriptorEntityProvider
     */
    public static function fromEntitiesDescriptorFile($filename, $entityId)
    {
        return new EntitiesDescriptorEntityProvider(
            new FileEntitiesDescriptorProvider($filename),
            $entityId
        );
    }
}
