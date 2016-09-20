<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Credential;

use LightSaml\Criteria\CriteriaSet;
use LightSaml\Credential\CredentialInterface;

class CredentialResolverQuery extends CriteriaSet
{
    /** @var CredentialResolverInterface */
    private $resolver;

    /** @var CredentialInterface[] */
    private $arrCredentials;

    /**
     * @param CredentialResolverInterface $resolver
     */
    public function __construct(CredentialResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return CredentialResolverQuery
     */
    public function resolve()
    {
        $this->arrCredentials = $this->resolver->resolve($this);

        return $this;
    }

    /**
     * @return CredentialInterface|null
     */
    public function firstCredential()
    {
        return reset($this->arrCredentials) ?: null;
    }

    /**
     * @return CredentialInterface[]
     */
    public function allCredentials()
    {
        return $this->arrCredentials;
    }

    /**
     * @return CredentialInterface[]
     */
    public function getPublicKeys()
    {
        $result = array();
        foreach ($this->arrCredentials as $credential) {
            if ($credential instanceof CredentialInterface) {
                $publicKey = $credential->getPublicKey();
                if ($publicKey) {
                    $result[] = $credential;
                }
            } else {
                throw new \InvalidArgumentException('Expected CredentialInterface');
            }
        }

        return $result;
    }

    /**
     * @return CredentialInterface[]
     */
    public function getPrivateKeys()
    {
        $result = array();
        foreach ($this->arrCredentials as $credential) {
            if ($credential instanceof CredentialInterface) {
                $privateKey = $credential->getPrivateKey();
                if ($privateKey) {
                    $result[] = $credential;
                }
            } else {
                throw new \InvalidArgumentException('Expected CredentialInterface');
            }
        }

        return $result;
    }
}
