<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Store\Credential\Factory;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Error\LightSamlBuildException;
use LightSaml\Store\Credential\CompositeCredentialStore;
use LightSaml\Store\Credential\CredentialStoreInterface;
use LightSaml\Store\Credential\MetadataCredentialStore;
use LightSaml\Store\Credential\StaticCredentialStore;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;

class CredentialFactory
{
    /** @var CredentialInterface[] */
    private $extraCredentials = [];

    /**
     * @param CredentialInterface $credential
     *
     * @return CredentialFactory
     */
    public function addExtraCredential(CredentialInterface $credential)
    {
        $this->extraCredentials[] = $credential;

        return $this;
    }

    /**
     * @param EntityDescriptorStoreInterface $idpEntityDescriptorStore
     * @param EntityDescriptorStoreInterface $spEntityDescriptorStore
     * @param string                         $ownEntityId
     * @param CredentialStoreInterface       $ownCredentialStore
     * @param CredentialInterface[]          $extraCredentials
     *
     * @return CompositeCredentialStore
     */
    public function buildFromOwnCredentialStore(
        EntityDescriptorStoreInterface $idpEntityDescriptorStore,
        EntityDescriptorStoreInterface $spEntityDescriptorStore,
        $ownEntityId,
        CredentialStoreInterface $ownCredentialStore,
        array $extraCredentials = null
    ) {
        return $this->build(
            $idpEntityDescriptorStore,
            $spEntityDescriptorStore,
            $ownCredentialStore->getByEntityId($ownEntityId),
            $extraCredentials
        );
    }

    /**
     * @param EntityDescriptorStoreInterface $idpEntityDescriptorStore
     * @param EntityDescriptorStoreInterface $spEntityDescriptorStore
     * @param CredentialInterface[]          $ownCredentials
     * @param CredentialInterface[]          $extraCredentials
     *
     * @return CompositeCredentialStore
     */
    public function build(
        EntityDescriptorStoreInterface $idpEntityDescriptorStore,
        EntityDescriptorStoreInterface $spEntityDescriptorStore,
        array $ownCredentials,
        array $extraCredentials = null
    ) {
        if (empty($ownCredentials)) {
            throw new LightSamlBuildException('There are no own credentials');
        }

        $store = new CompositeCredentialStore();
        $store->add(new MetadataCredentialStore($idpEntityDescriptorStore));
        $store->add(new MetadataCredentialStore($spEntityDescriptorStore));

        $ownCredentialsStore = new StaticCredentialStore();
        foreach ($ownCredentials as $credential) {
            $ownCredentialsStore->add($credential);
        }
        $store->add($ownCredentialsStore);

        $extraCredentialsStore = new StaticCredentialStore();
        $store->add($extraCredentialsStore);
        foreach ($this->extraCredentials as $credential) {
            $extraCredentialsStore->add($credential);
        }
        if ($extraCredentials) {
            foreach ($extraCredentials as $credential) {
                $extraCredentialsStore->add($credential);
            }
        }

        return $store;
    }
}
