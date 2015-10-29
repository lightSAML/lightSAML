<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Build\Container;

use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use LightSaml\Credential\CredentialInterface;

interface OwnContainerInterface
{
    /**
     * @return EntityDescriptorProviderInterface
     */
    public function getOwnEntityDescriptorProvider();

    /**
     * @return CredentialInterface[]
     */
    public function getOwnCredentials();
}
