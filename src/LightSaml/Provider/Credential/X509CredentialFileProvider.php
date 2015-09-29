<?php

namespace LightSaml\Provider\Credential;

use LightSaml\Credential\X509Credential;
use LightSaml\Model\Security\KeyHelper;
use LightSaml\Model\Security\X509Certificate;

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

    /** @var  X509Credential */
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
     * @return CredentialProviderInterface
     */
    public function get()
    {
        if (null == $this->credential) {
            $this->credential = new X509Credential(
                X509Certificate::fromFile($this->certificatePath),
                KeyHelper::createPrivateKey($this->privateKeyPath, $this->privateKeyPassword, true)
            );
        }

        return $this->credential;
    }
}
