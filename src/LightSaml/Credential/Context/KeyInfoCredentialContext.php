<?php

namespace LightSaml\Credential\Context;

class KeyInfoCredentialContext implements CredentialContextInterface
{
    /** @var  \XMLSecurityKey */
    protected $keyInfo;

    /**
     * @param \XMLSecurityKey $keyInfo
     */
    public function __construct(\XMLSecurityKey $keyInfo)
    {
        $this->keyInfo = $keyInfo;
    }

    /**
     * @return \XMLSecurityKey
     */
    public function getKeyInfo()
    {
        return $this->keyInfo;
    }
}
