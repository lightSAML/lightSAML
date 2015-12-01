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

interface CredentialInterface
{
    /**
     * @return string
     */
    public function getEntityId();

    /**
     * One of UsageType constants.
     *
     * @return string|null
     */
    public function getUsageType();

    /**
     * @return string[]
     */
    public function getKeyNames();

    /**
     * @return XMLSecurityKey|null
     */
    public function getPublicKey();

    /**
     * @return XMLSecurityKey|null
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
