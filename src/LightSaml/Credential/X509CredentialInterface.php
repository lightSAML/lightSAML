<?php

namespace LightSaml\Credential;

use LightSaml\Model\Security\X509Certificate;

interface X509CredentialInterface extends CredentialInterface
{
    /**
     * @return X509Certificate
     */
    public function getCertificate();
}
