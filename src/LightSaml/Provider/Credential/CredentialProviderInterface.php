<?php

namespace LightSaml\Provider\Credential;

use LightSaml\Credential\CredentialInterface;

interface CredentialProviderInterface
{
    /**
     * @return CredentialInterface
     */
    public function get();
}
