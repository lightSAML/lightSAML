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
