<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Store\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\X509Credential;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;

class X509FileCredentialStore implements CredentialStoreInterface
{
    /** @var string */
    private $entityId;

    /** @var string */
    private $certificatePath;

    /** @var string */
    private $keyPath;

    /** @var string */
    private $password;

    /** @var X509Credential */
    private $credential;

    /**
     * @param string $entityId
     * @param string $certificatePath
     * @param string $keyPath
     * @param string $password
     */
    public function __construct($entityId, $certificatePath, $keyPath, $password)
    {
        $this->entityId = $entityId;
        $this->certificatePath = $certificatePath;
        $this->keyPath = $keyPath;
        $this->password = $password;
    }

    /**
     * @param string $entityId
     *
     * @return CredentialInterface[]
     */
    public function getByEntityId($entityId)
    {
        if ($entityId != $this->entityId) {
            return [];
        }

        if (null == $this->credential) {
            $certificate = X509Certificate::fromFile($this->certificatePath);
            $this->credential = new X509Credential(
                $certificate,
                KeyHelper::createPrivateKey($this->keyPath, $this->password, true, $certificate->getSignatureAlgorithm())
            );
            $this->credential->setEntityId($this->entityId);
        }

        return [$this->credential];
    }
}
