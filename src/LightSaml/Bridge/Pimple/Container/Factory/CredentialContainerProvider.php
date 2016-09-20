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

use LightSaml\Bridge\Pimple\Container\CredentialContainer;
use LightSaml\Build\Container\OwnContainerInterface;
use LightSaml\Build\Container\PartyContainerInterface;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Error\LightSamlBuildException;
use LightSaml\Store\Credential\Factory\CredentialFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CredentialContainerProvider implements ServiceProviderInterface
{
    /** @var PartyContainerInterface */
    private $partyContainer;

    /** @var OwnContainerInterface */
    private $ownContainer;

    /** @var CredentialInterface[] */
    private $extraCredentials = array();

    /**
     * @param PartyContainerInterface $partyContainer
     * @param OwnContainerInterface   $ownContainer
     */
    public function __construct(PartyContainerInterface $partyContainer, OwnContainerInterface $ownContainer)
    {
        $this->ownContainer = $ownContainer;
        $this->partyContainer = $partyContainer;
    }

    /**
     * @param CredentialInterface $credential
     *
     * @return CredentialContainerProvider
     */
    public function addExtraCredential(CredentialInterface $credential)
    {
        if (null === $credential->getEntityId()) {
            throw new \InvalidArgumentException('Extra credential must have entityID');
        }

        $this->extraCredentials[] = $credential;

        return $this;
    }

    /**
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $ownCredentials = $this->ownContainer->getOwnCredentials();
        if (empty($ownCredentials)) {
            throw new LightSamlBuildException('There are no own credentials');
        }

        $pimple[CredentialContainer::CREDENTIAL_STORE] = function () {
            $factory = new CredentialFactory();

            return $factory->build(
                $this->partyContainer->getIdpEntityDescriptorStore(),
                $this->partyContainer->getSpEntityDescriptorStore(),
                $this->ownContainer->getOwnCredentials(),
                $this->extraCredentials
            );
        };
    }
}
