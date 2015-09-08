<?php

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
