<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Bridge\Pimple\Container\Factory;

use LightSaml\Bridge\Pimple\Container\OwnContainer;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Error\LightSamlBuildException;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OwnContainerProvider implements ServiceProviderInterface
{
    /** @var CredentialInterface[] */
    private $ownCredentials = array();

    /** @var EntityDescriptorProviderInterface */
    private $ownEntityDescriptorProvider;

    /**
     * @param EntityDescriptorProviderInterface $ownEntityDescriptorProvider
     * @param CredentialInterface[]             $ownCredentials
     */
    public function __construct(EntityDescriptorProviderInterface $ownEntityDescriptorProvider, array $ownCredentials = null)
    {
        $this->ownEntityDescriptorProvider = $ownEntityDescriptorProvider;
        if ($ownCredentials) {
            foreach ($ownCredentials as $credential) {
                $this->addOwnCredential($credential);
            }
        }
    }

    /**
     * @param CredentialInterface $credential
     *
     * @return OwnContainerProvider
     */
    public function addOwnCredential(CredentialInterface $credential)
    {
        if (null == $credential->getPrivateKey()) {
            throw new LightSamlBuildException('Own credential must have private key');
        }

        $this->ownCredentials[] = $credential;

        return $this;
    }

    /**
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple[OwnContainer::OWN_CREDENTIALS] = function () {
            return $this->ownCredentials;
        };

        $pimple[OwnContainer::OWN_ENTITY_DESCRIPTOR_PROVIDER] = function () {
            return $this->ownEntityDescriptorProvider;
        };
    }
}
