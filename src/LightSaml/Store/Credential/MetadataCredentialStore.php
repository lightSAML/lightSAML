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

use LightSaml\Credential\Context\CredentialContextSet;
use LightSaml\Credential\Context\MetadataCredentialContext;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\X509Credential;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\SSODescriptor;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;

class MetadataCredentialStore implements CredentialStoreInterface
{
    /** @var EntityDescriptorStoreInterface */
    protected $entityDescriptorProvider;

    public function __construct(EntityDescriptorStoreInterface $entityDescriptorProvider)
    {
        $this->entityDescriptorProvider = $entityDescriptorProvider;
    }

    /**
     * @param string $entityId
     *
     * @return CredentialInterface[]
     */
    public function getByEntityId($entityId)
    {
        $entityDescriptor = $this->entityDescriptorProvider->get($entityId);
        if (false == $entityDescriptor) {
            return [];
        }

        return $this->extractCredentials($entityDescriptor);
    }

    /**
     * @return CredentialInterface[]
     */
    protected function extractCredentials(EntityDescriptor $entityDescriptor)
    {
        $result = [];

        foreach ($entityDescriptor->getAllIdpSsoDescriptors() as $idpDescriptor) {
            $this->handleDescriptor($idpDescriptor, $entityDescriptor, $result);
        }
        foreach ($entityDescriptor->getAllSpSsoDescriptors() as $spDescriptor) {
            $this->handleDescriptor($spDescriptor, $entityDescriptor, $result);
        }

        return $result;
    }

    protected function handleDescriptor(SSODescriptor $ssoDescriptor, EntityDescriptor $entityDescriptor, array &$result)
    {
        foreach ($ssoDescriptor->getAllKeyDescriptors() as $keyDescriptor) {
            $credential = (new X509Credential($keyDescriptor->getCertificate()))
                ->setEntityId($entityDescriptor->getEntityID())
                ->addKeyName($keyDescriptor->getCertificate()->getName())
                ->setCredentialContext(new CredentialContextSet([
                    new MetadataCredentialContext($keyDescriptor, $ssoDescriptor, $entityDescriptor),
                ]))
                ->setUsageType($keyDescriptor->getUse())
            ;

            $result[] = $credential;
        }
    }
}
