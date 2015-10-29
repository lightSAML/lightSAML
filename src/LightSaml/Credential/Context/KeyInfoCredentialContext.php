<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
