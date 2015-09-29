<?php

namespace LightSaml\Provider\Credential;

interface CredentialProviderInterface
{
    /**
     * @return CredentialProviderInterface
     */
    public function get();
}
