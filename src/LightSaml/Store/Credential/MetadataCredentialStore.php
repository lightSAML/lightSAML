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

use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\SSODescriptor;
use LightSaml\Credential\Context\CredentialContextSet;
use LightSaml\Credential\Context\MetadataCredentialContext;
use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\X509Credential;

class MetadataCredentialStore implements CredentialStoreInterface
{
    /** @var EntityDescriptorStoreInterface */
    protected $entityDescriptorProvider;

    /**
     * @param EntityDescriptorStoreInterface $entityDescriptorProvider
     */
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
            return array();
        }

        return $this->extractCredentials($entityDescriptor);
    }

    /**
     * @param EntityDescriptor $entityDescriptor
     *
     * @return CredentialInterface[]
     */
    protected function extractCredentials(EntityDescriptor $entityDescriptor)
    {
        $result = array();

        foreach ($entityDescriptor->getAllIdpSsoDescriptors() as $idpDescriptor) {
            $this->handleDescriptor($idpDescriptor, $entityDescriptor, $result);
        }
        foreach ($entityDescriptor->getAllSpSsoDescriptors() as $spDescriptor) {
            $this->handleDescriptor($spDescriptor, $entityDescriptor, $result);
        }

        return $result;
    }

    /**
     * @param SSODescriptor    $ssoDescriptor
     * @param EntityDescriptor $entityDescriptor
     * @param array            $result
     */
    protected function handleDescriptor(SSODescriptor $ssoDescriptor, EntityDescriptor $entityDescriptor, array &$result)
    {
        foreach ($ssoDescriptor->getAllKeyDescriptors() as $keyDescriptor) {
            $credential = (new X509Credential($keyDescriptor->getCertificate()))
                ->setEntityId($entityDescriptor->getEntityID())
                ->addKeyName($keyDescriptor->getCertificate()->getName())
                ->setCredentialContext(new CredentialContextSet(array(
                    new MetadataCredentialContext($keyDescriptor, $ssoDescriptor, $entityDescriptor),
                )))
                ->setUsageType($keyDescriptor->getUse())
            ;

            $result[] = $credential;
        }
    }
}
