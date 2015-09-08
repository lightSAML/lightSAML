<?php

namespace LightSaml\Bridge\Pimple\Container;

use LightSaml\Build\Container\CredentialContainerInterface;
use LightSaml\Store\Credential\CredentialStoreInterface;

class CredentialContainer extends AbstractPimpleContainer implements CredentialContainerInterface
{
    const CREDENTIAL_STORE = 'lightsaml.container.credential_store';

    /**
     * @return CredentialStoreInterface
     */
    public function getCredentialStore()
    {
        return $this->pimple[self::CREDENTIAL_STORE];
    }
}
