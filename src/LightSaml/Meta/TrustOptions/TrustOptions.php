<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Meta\TrustOptions;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class TrustOptions
{
    /** @var bool */
    protected $signAuthnRequest = false;

    /** @var bool */
    protected $encryptAuthnRequest = false;

    /** @var bool */
    protected $signAssertions = true;

    /** @var bool */
    protected $encryptAssertions = true;

    /** @var bool */
    protected $signResponse = true;

    /** @var string */
    protected $signatureDigestAlgorithm = XMLSecurityDSig::SHA1;

    /** @var string */
    protected $blockEncryptionAlgorithm = XMLSecurityKey::AES128_CBC;

    /** @var string */
    protected $keyTransportEncryptionAlgorithm = XMLSecurityKey::RSA_OAEP_MGF1P;

    /**
     * @return bool
     */
    public function getEncryptAssertions()
    {
        return $this->encryptAssertions;
    }

    /**
     * @param bool $encryptAssertions
     *
     * @return TrustOptions
     */
    public function setEncryptAssertions($encryptAssertions)
    {
        $this->encryptAssertions = (bool) $encryptAssertions;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEncryptAuthnRequest()
    {
        return $this->encryptAuthnRequest;
    }

    /**
     * @param bool $encryptAuthnRequest
     *
     * @return TrustOptions
     */
    public function setEncryptAuthnRequest($encryptAuthnRequest)
    {
        $this->encryptAuthnRequest = (bool) $encryptAuthnRequest;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSignAssertions()
    {
        return $this->signAssertions;
    }

    /**
     * @param bool $signAssertions
     *
     * @return TrustOptions
     */
    public function setSignAssertions($signAssertions)
    {
        $this->signAssertions = (bool) $signAssertions;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSignAuthnRequest()
    {
        return $this->signAuthnRequest;
    }

    /**
     * @param bool $signAuthnRequest
     *
     * @return TrustOptions
     */
    public function setSignAuthnRequest($signAuthnRequest)
    {
        $this->signAuthnRequest = (bool) $signAuthnRequest;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSignResponse()
    {
        return $this->signResponse;
    }

    /**
     * @param bool $signResponse
     *
     * @return TrustOptions
     */
    public function setSignResponse($signResponse)
    {
        $this->signResponse = (bool) $signResponse;

        return $this;
    }

    /**
     * @return string
     */
    public function getSignatureDigestAlgorithm()
    {
        return $this->signatureDigestAlgorithm;
    }

    /**
     * @param string $signatureDigestAlgorithm
     *
     * @return TrustOptions
     */
    public function setSignatureDigestAlgorithm($signatureDigestAlgorithm)
    {
        $this->signatureDigestAlgorithm = $signatureDigestAlgorithm;

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockEncryptionAlgorithm()
    {
        return $this->blockEncryptionAlgorithm;
    }

    /**
     * @param string $blockEncryptionAlgorithm
     *
     * @return TrustOptions
     */
    public function setBlockEncryptionAlgorithm($blockEncryptionAlgorithm)
    {
        $this->blockEncryptionAlgorithm = $blockEncryptionAlgorithm;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyTransportEncryptionAlgorithm()
    {
        return $this->keyTransportEncryptionAlgorithm;
    }

    /**
     * @param string $keyTransportEncryptionAlgorithm
     *
     * @return TrustOptions
     */
    public function setKeyTransportEncryptionAlgorithm($keyTransportEncryptionAlgorithm)
    {
        $this->keyTransportEncryptionAlgorithm = $keyTransportEncryptionAlgorithm;

        return $this;
    }
}
