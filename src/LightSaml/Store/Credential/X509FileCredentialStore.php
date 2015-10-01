<?php

namespace LightSaml\Store\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\X509Credential;
use LightSaml\Model\Security\KeyHelper;
use LightSaml\Model\Security\X509Certificate;

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
            $this->credential = new X509Credential(
                X509Certificate::fromFile($this->certificatePath),
                KeyHelper::createPrivateKey($this->keyPath, $this->password, true)
            );
            $this->credential->setEntityId($this->entityId);
        }

        return [$this->credential];
    }
}
