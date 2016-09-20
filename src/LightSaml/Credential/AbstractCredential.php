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

use LightSaml\Credential\Context\CredentialContextSet;
use RobRichards\XMLSecLibs\XMLSecurityKey;

abstract class AbstractCredential implements CredentialInterface
{
    /** @var string */
    private $entityId;

    /** @var string */
    private $usageType;

    /** @var string[] */
    private $keyNames = array();

    /** @var XMLSecurityKey|null */
    private $publicKey;

    /** @var XMLSecurityKey|null */
    private $privateKey;

    /** @var string|null */
    private $secretKey;

    /** @var CredentialContextSet */
    private $credentialContext;

    public function __construct()
    {
        $this->credentialContext = new CredentialContextSet();
    }

    /**
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * One of UsageType constants.
     *
     * @return string|null
     */
    public function getUsageType()
    {
        return $this->usageType;
    }

    /**
     * @return string[]
     */
    public function getKeyNames()
    {
        return $this->keyNames;
    }

    /**
     * @return XMLSecurityKey|null
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @return XMLSecurityKey|null
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @return string|null
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @return CredentialContextSet
     */
    public function getCredentialContext()
    {
        return $this->credentialContext;
    }

    /**
     * @param CredentialContextSet $credentialContext
     *
     * @return AbstractCredential
     */
    public function setCredentialContext(CredentialContextSet $credentialContext)
    {
        $this->credentialContext = $credentialContext;

        return $this;
    }

    /**
     * @param string $entityId
     *
     * @return AbstractCredential
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * @param \string[] $keyNames
     *
     * @return AbstractCredential
     */
    public function setKeyNames(array $keyNames)
    {
        $this->keyNames = $keyNames;

        return $this;
    }

    /**
     * @param string $keyName
     *
     * @return AbstractCredential
     */
    public function addKeyName($keyName)
    {
        $keyName = trim($keyName);
        if ($keyName) {
            $this->keyNames[] = $keyName;
        }

        return $this;
    }

    /**
     * @param null|XMLSecurityKey $privateKey
     *
     * @return AbstractCredential
     */
    public function setPrivateKey(XMLSecurityKey $privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * @param null|XMLSecurityKey $publicKey
     *
     * @return AbstractCredential
     */
    public function setPublicKey(XMLSecurityKey $publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @param null|string $secretKey
     *
     * @return AbstractCredential
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * @param string $usageType
     *
     * @return AbstractCredential
     */
    public function setUsageType($usageType)
    {
        $this->usageType = $usageType;

        return $this;
    }
}
