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
use LightSaml\Builder\Action\Profile\SingleSignOn\Sp\SsoSpSendLogoutRequestActionBuilder;
use LightSaml\Builder\Profile\AbstractProfileBuilder;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Profile\Profiles;

class SsoSpSendLogoutRequestProfileBuilder extends AbstractProfileBuilder
{
    /** @var string */
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

    /**
     * @return ProfileContext
     */
    public function buildContext()
    {
        $result = parent::buildContext();

        $idpEd = $this->container->getPartyContainer()->getIdpEntityDescriptorStore()->get($this->idpEntityId);

        if (!$idpEd) {
            throw new \RuntimeException(sprintf('Unknown IDP "%s"', $this->idpEntityId));
        }

        $result
          ->getPartyEntityContext()
          ->setEntityDescriptor($idpEd)
          ->setTrustOptions($this->getTrustOptions());

        return $result;
    }

    /**
     * @return TrustOptions
     */
    protected function getTrustOptions()
    {
        return $this->container->getPartyContainer()->getTrustOptionsStore()->get($this->idpEntityId) ?: new TrustOptions();
    }

    /**
     * @return \LightSaml\Builder\Action\ActionBuilderInterface
     */
    protected function getActionBuilder()
    {
        return new SsoSpSendLogoutRequestActionBuilder($this->container);
    }

    /**
     * @return string
     */
    protected function getProfileId()
    {
        return Profiles::SSO_SP_SEND_LOGOUT_REQUEST;
    }

    /**
     * @return string
     */
    protected function getProfileRole()
    {
        return ProfileContext::ROLE_SP;
    }
}
