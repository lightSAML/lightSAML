<?php

namespace LightSaml\Bridge\Pimple\Container;

use LightSaml\Build\Container\OwnContainerInterface;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use LightSaml\Credential\CredentialInterface;

class OwnContainer extends AbstractPimpleContainer implements OwnContainerInterface
{
    const OWN_ENTITY_DESCRIPTOR_PROVIDER = 'lightsaml.container.own_entity_descriptor_provider';
    const OWN_CREDENTIALS = 'lightsaml.container.own_credentials';

    /**
     * @return EntityDescriptorProviderInterface
     */
    public function getOwnEntityDescriptorProvider()
    {
        return $this->pimple[self::OWN_ENTITY_DESCRIPTOR_PROVIDER];
    }

    /**
     * @return CredentialInterface[]
     */
    public function getOwnCredentials()
    {
        return $this->pimple[self::OWN_CREDENTIALS];
    }
}
