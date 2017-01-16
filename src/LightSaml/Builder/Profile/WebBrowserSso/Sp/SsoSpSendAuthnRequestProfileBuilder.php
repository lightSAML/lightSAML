<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\Profile\WebBrowserSso\Sp;

use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Builder\Action\Profile\SingleSignOn\Sp\SsoSpSendAuthnRequestActionBuilder;
use LightSaml\Builder\Profile\AbstractProfileBuilder;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Profile\Profiles;

class SsoSpSendAuthnRequestProfileBuilder extends AbstractProfileBuilder
{
    protected $idpEntityId;

    /**
     * @param BuildContainerInterface $buildContainer
     * @param string                  $idpEntityId
     */
    public function __construct(BuildContainerInterface $buildContainer, $idpEntityId)
    {
        parent::__construct($buildContainer);

        $this->idpEntityId = $idpEntityId;
    }

    public function buildContext()
    {
        $result = parent::buildContext();

        $idpEd = $this->container->getPartyContainer()->getIdpEntityDescriptorStore()->get($this->idpEntityId);
        if (false == $idpEd) {
            throw new \RuntimeException(sprintf('Unknown IDP "%s"', $this->idpEntityId));
        }

        $trustOptions = $this->getTrustOptions($idpEd);

        $result->getPartyEntityContext()
            ->setEntityDescriptor($idpEd)
            ->setTrustOptions($trustOptions)
        ;

        return $result;
    }

    /**
     * @return string
     */
    protected function getProfileId()
    {
        return Profiles::SSO_SP_SEND_AUTHN_REQUEST;
    }

    /**
     * @return string
     */
    protected function getProfileRole()
    {
        return ProfileContext::ROLE_SP;
    }

    /**
     * @return \LightSaml\Builder\Action\ActionBuilderInterface
     */
    protected function getActionBuilder()
    {
        return new SsoSpSendAuthnRequestActionBuilder($this->container);
    }

    /**
     * @param EntityDescriptor $idpEd
     *
     * @return TrustOptions
     */
    private function getTrustOptions(EntityDescriptor $idpEd)
    {
        $trustOptions = $this->container->getPartyContainer()->getTrustOptionsStore()->get($this->idpEntityId) ?: new TrustOptions();

        $wantAuthnRequestsSigned = $idpEd->getFirstIdpSsoDescriptor()->getWantAuthnRequestsSigned();

        if ($wantAuthnRequestsSigned !== null) {
            $trustOptions->setSignAuthnRequest($wantAuthnRequestsSigned);
        }

        return $trustOptions;
    }
}
