<?php

namespace LightSaml\Credential;

use LightSaml\Credential\X509Certificate;

interface X509CredentialInterface extends CredentialInterface
{
    /**
     * @return X509Certificate
     */
    public function getCertificate();
}
