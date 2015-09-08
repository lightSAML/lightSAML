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
     * @param \XMLSecurityKey[] $keyCandidates
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException                              If some element of $keys array is not \XMLSecurityKey
     * @throws \LightSaml\Error\LightSamlSecurityException If validation fails
     *
     * @return \XMLSecurityKey|null Returns key that validated the signature or null if validation was not performed
     */
    public function validateMulti(array $keyCandidates)
    {
        $lastException = null;

        foreach ($keyCandidates as $key) {
            if ($key instanceof CredentialInterface) {
                $key = $key->getPublicKey();
            }
            if (false == $key instanceof \XMLSecurityKey) {
                throw new \InvalidArgumentException('Expected XMLSecurityKey');
            }

            try {
                $result = $this->validate($key);

                if ($result === false) {
                    return null;
                }

                return $key;
            } catch (LightSamlSecurityException $ex) {
                $lastException = $ex;
            }
        }

        if ($lastException) {
            throw $lastException;
        } else {
            throw new LightSamlSecurityException('No key provided for signature verification');
        }
    }
}
