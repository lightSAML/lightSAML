<?php

namespace LightSaml\Credential;

use LightSaml\Credential\Context\CredentialContextSet;

interface CredentialInterface
{
    /**
     * @return string
     */
    public function getEntityId();

    /**
     * One of UsageType constants
     * @return string|null
     */
    public function getUsageType();

    /**
     * @return string[]
     */
    public function getKeyNames();

    /**
     * @return \XMLSecurityKey|null
     */
    public function getPublicKey();

    /**
     * @return \XMLSecurityKey|null
     */
    public function getPrivateKey();

    /**
     * @return string|null
     */
    public function getSecretKey();

    /**
     * @return CredentialContextSet
     */
    public function getCredentialContext();
}
