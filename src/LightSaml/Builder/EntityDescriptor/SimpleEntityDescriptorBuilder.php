<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\EntityDescriptor;

use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\RoleDescriptor;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use LightSaml\SamlConstants;
use LightSaml\Credential\X509Certificate;

class SimpleEntityDescriptorBuilder implements EntityDescriptorProviderInterface
{
    /** @var string */
    protected $entityId;

    /** @var string */
    protected $acsUrl;

    /** @var string[] */
    protected $acsBindings;

    /** @var string */
    protected $ssoUrl;

    /** @var string[] */
    protected $ssoBindings;

    /** @var string[]|null */
    protected $use;

    /** @var X509Certificate */
    protected $ownCertificate;

    /** @var EntityDescriptor */
    private $entityDescriptor;

    /**
     * @param string          $entityId
     * @param string          $acsUrl
     * @param string          $ssoUrl
     * @param X509Certificate $ownCertificate
     * @param string[]        $acsBindings
     * @param string[]        $ssoBindings
     * @param string[]|null   $use
     */
    public function __construct(
        $entityId,
        $acsUrl,
        $ssoUrl,
        X509Certificate $ownCertificate,
        array $acsBindings = array(SamlConstants::BINDING_SAML2_HTTP_POST),
        array $ssoBindings = array(SamlConstants::BINDING_SAML2_HTTP_POST, SamlConstants::BINDING_SAML2_HTTP_REDIRECT),
        $use = array(KeyDescriptor::USE_ENCRYPTION, KeyDescriptor::USE_SIGNING)
    ) {
        $this->entityId = $entityId;
        $this->acsUrl = $acsUrl;
        $this->ssoUrl = $ssoUrl;
        $this->ownCertificate = $ownCertificate;
        $this->acsBindings = $acsBindings;
        $this->ssoBindings = $ssoBindings;
        $this->use = $use;
    }

    /**
     * @return EntityDescriptor
     */
    public function get()
    {
        if (null === $this->entityDescriptor) {
            $this->entityDescriptor = $this->getEntityDescriptor();
            if (false === $this->entityDescriptor instanceof EntityDescriptor) {
                throw new \LogicException('Expected EntityDescriptor');
            }
        }

        return $this->entityDescriptor;
    }

    /**
     * @return EntityDescriptor
     */
    protected function getEntityDescriptor()
    {
        $entityDescriptor = new EntityDescriptor();
        $entityDescriptor->setEntityID($this->entityId);

        $spSsoDescriptor = $this->getSpSsoDescriptor();
        if ($spSsoDescriptor) {
            $entityDescriptor->addItem($spSsoDescriptor);
        }

        $idpSsoDescriptor = $this->getIdpSsoDescriptor();
        if ($idpSsoDescriptor) {
            $entityDescriptor->addItem($idpSsoDescriptor);
        }

        return $entityDescriptor;
    }

    /**
     * @return SpSsoDescriptor|null
     */
    protected function getSpSsoDescriptor()
    {
        if (null === $this->acsUrl) {
            return null;
        }

        $spSso = new SpSsoDescriptor();

        foreach ($this->acsBindings as $index => $biding) {
            $acs = new AssertionConsumerService();
            $acs->setIndex($index)->setLocation($this->acsUrl)->setBinding($biding);
            $spSso->addAssertionConsumerService($acs);
        }

        $this->addKeyDescriptors($spSso);

        return $spSso;
    }

    /**
     * @return IdpSsoDescriptor
     */
    protected function getIdpSsoDescriptor()
    {
        if (null === $this->ssoUrl) {
            return null;
        }

        $idpSso = new IdpSsoDescriptor();

        foreach ($this->ssoBindings as $index => $binding) {
            $sso = new SingleSignOnService();
            $sso
                ->setLocation($this->ssoUrl)
                ->setBinding($binding);
            $idpSso->addSingleSignOnService($sso);
        }

        $this->addKeyDescriptors($idpSso);

        return $idpSso;
    }

    /**
     * @param RoleDescriptor $descriptor
     */
    protected function addKeyDescriptors(RoleDescriptor $descriptor)
    {
        if ($this->use) {
            foreach ($this->use as $use) {
                $kd = new KeyDescriptor();
                $kd->setUse($use);
                $kd->setCertificate($this->ownCertificate);

                $descriptor->addKeyDescriptor($kd);
            }
        } else {
            $kd = new KeyDescriptor();
            $kd->setCertificate($this->ownCertificate);

            $descriptor->addKeyDescriptor($kd);
        }
    }
}
