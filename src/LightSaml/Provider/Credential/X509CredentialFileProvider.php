<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Provider\Credential;

use LightSaml\Credential\X509Credential;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;

class X509CredentialFileProvider implements CredentialProviderInterface
{
    /** @var string */
    private $entityId;

    /** @var string */
    private $certificatePath;

    /** @var string */
    private $privateKeyPath;

    /** @var string */
    private $privateKeyPassword;

    /** @var X509Credential */
    private $credential;

    /**
     * @param string $entityId
     * @param string $certificatePath
     * @param string $privateKeyPath
     * @param        $privateKeyPassword
     */
    public function __construct($entityId, $certificatePath, $privateKeyPath, $privateKeyPassword)
    {
        $this->entityId = $entityId;
        $this->certificatePath = $certificatePath;
        $this->privateKeyPath = $privateKeyPath;
        $this->privateKeyPassword = $privateKeyPassword;
    }

    /**
     * @return X509Credential
     */
    public function get()
    {
        if (null == $this->credential) {
            $this->credential = new X509Credential(
                X509Certificate::fromFile($this->certificatePath),
                KeyHelper::createPrivateKey($this->privateKeyPath, $this->privateKeyPassword, true)
            );
            $this->credential->setEntityId($this->entityId);
        }

        return $this->credential;
    }
}
