<?php

namespace LightSaml\Model\XmlDSig;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Error\LightSamlSecurityException;

abstract class AbstractSignatureReader extends Signature
{
    /** @var  \XMLSecurityKey|null */
    protected $key;

    /**
     * @param \XMLSecurityKey $key
     *
     * @return bool True if validated, False if validation was not performed
     *
     * @throws \LightSaml\Error\LightSamlSecurityException If validation fails
     */
    abstract public function validate(\XMLSecurityKey $key);

    /**
     * @return \XMLSecurityKey|null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param CredentialInterface[] $credentialCandidates
     *
     * @throws \InvalidArgumentException                   If element of $credentialCandidates array is not CredentialInterface
     * @throws \LightSaml\Error\LightSamlSecurityException If validation fails
     *
     * @return CredentialInterface|null Returns credential that validated the signature or null if validation was not performed
     */
    public function validateMulti(array $credentialCandidates)
    {
        $lastException = null;

        foreach ($credentialCandidates as $credential) {
            if (false == $credential instanceof CredentialInterface) {
                throw new \InvalidArgumentException('Expected CredentialInterface');
            }
            if (null == $credential->getPublicKey()) {
                continue;
            }

            try {
                $result = $this->validate($credential->getPublicKey());

                if ($result === false) {
                    return null;
                }

                return $credential;
            } catch (LightSamlSecurityException $ex) {
                $lastException = $ex;
            }
        }

        if ($lastException) {
            throw $lastException;
        } else {
            throw new LightSamlSecurityException('No public key available for signature verification');
        }
    }
}
