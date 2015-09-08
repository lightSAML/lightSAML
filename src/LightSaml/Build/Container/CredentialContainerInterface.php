<?php

namespace LightSaml\Build\Container;

use LightSaml\Store\Credential\CredentialStoreInterface;

interface CredentialContainerInterface
{
    /**
     * @return CredentialStoreInterface
     */
    public function getCredentialStore();
}
