<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\XmlDSig;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\KeyHelper;
use LightSaml\Error\LightSamlSecurityException;
use RobRichards\XMLSecLibs\XMLSecurityKey;

abstract class AbstractSignatureReader extends Signature
{
    /** @var XMLSecurityKey|null */
    protected $key;

    /**
     * @param XMLSecurityKey $key
     *
     * @return bool True if validated, False if validation was not performed
     *
     * @throws \LightSaml\Error\LightSamlSecurityException If validation fails
     */
    abstract public function validate(XMLSecurityKey $key);

    /**
     * @return XMLSecurityKey|null
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
                    return;
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

    /**
     * @return string
     */
    abstract public function getAlgorithm();

    /**
     * @param XMLSecurityKey $key
     *
     * @return XMLSecurityKey
     */
    protected function castKeyIfNecessary(XMLSecurityKey $key)
    {
        $algorithm = $this->getAlgorithm();
        if ($algorithm != $key->type) {
            $key = KeyHelper::castKey($key, $algorithm);
        }

        return $key;
    }
}
