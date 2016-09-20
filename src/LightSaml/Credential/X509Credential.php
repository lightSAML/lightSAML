<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Credential;

use RobRichards\XMLSecLibs\XMLSecurityKey;

class X509Credential extends AbstractCredential implements X509CredentialInterface
{
    /** @var X509Certificate */
    protected $certificate;

    /**
     * @param X509Certificate $certificate
     * @param XMLSecurityKey  $privateKey
     */
    public function __construct(X509Certificate $certificate, XMLSecurityKey $privateKey = null)
    {
        parent::__construct();
        $this->certificate = $certificate;

        $this->setPublicKey(KeyHelper::createPublicKey($certificate));

        $this->setKeyNames(array($this->getCertificate()->getName()));

        if ($privateKey) {
            $this->setPrivateKey($privateKey);
        }
    }

    /**
     * @return X509Certificate
     */
    public function getCertificate()
    {
        return $this->certificate;
    }
}
