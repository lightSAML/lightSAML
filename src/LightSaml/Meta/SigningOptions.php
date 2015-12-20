<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Meta;

use LightSaml\Credential\X509Certificate;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class SigningOptions
{
    const CERTIFICATE_SUBJECT_NAME = 'subjectName';
    const CERTIFICATE_ISSUER_SERIAL = 'issuerSerial';

    /** @var bool */
    private $enabled = true;

    /** @var XMLSecurityKey */
    private $privateKey;

    /** @var X509Certificate */
    private $certificate;

    /** @var ParameterBag */
    private $certificateOptions;

    /**
     * @param XMLSecurityKey  $privateKey
     * @param X509Certificate $certificate
     */
    public function __construct(XMLSecurityKey $privateKey = null, X509Certificate $certificate = null)
    {
        $this->enabled = true;
        $this->privateKey = $privateKey;
        $this->certificate = $certificate;
        $this->certificateOptions = new ParameterBag();
    }

    /**
     * @return X509Certificate
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @param X509Certificate $certificate
     *
     * @return SigningOptions
     */
    public function setCertificate(X509Certificate $certificate = null)
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * @return XMLSecurityKey
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param XMLSecurityKey $privateKey
     *
     * @return SigningOptions
     */
    public function setPrivateKey(XMLSecurityKey $privateKey = null)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * @return ParameterBag
     */
    public function getCertificateOptions()
    {
        return $this->certificateOptions;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return SigningOptions
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;

        return $this;
    }
}
