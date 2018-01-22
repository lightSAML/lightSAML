<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Outbound;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Build\Container\PartyContainerInterface;
use LightSaml\Build\Container\StoreContainerInterface;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Protocol\LogoutRequest;
use Psr\Log\LoggerInterface;

class CreateLogoutRequestAction extends AbstractProfileAction
{
    /** @var PartyContainerInterface */
    protected $partyContainer;

    /** @var StoreContainerInterface */
    protected $storeContainer;

    /**
     * @param LoggerInterface         $logger
     * @param PartyContainerInterface $partyContainer
     * @param StoreContainerInterface $storeContainer
     */
    public function __construct(
      LoggerInterface $logger,
      PartyContainerInterface $partyContainer,
      StoreContainerInterface $storeContainer
    ) {
        parent::__construct($logger);
        $this->partyContainer = $partyContainer;
        $this->storeContainer = $storeContainer;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $request = new LogoutRequest();

        $destination = $this->partyContainer->getIdpEntityDescriptorStore()->get(0)
          ->getFirstIdpSsoDescriptor()->getFirstSingleLogoutService()->getLocation();

        $sessions = $this->storeContainer->getSsoStateStore()->get()->getSsoSessions();

        if (count($sessions) === 0) {
            throw new \LogicException('No active session was found.');
        }

        $session = $sessions[count($sessions) - 1];

        $request
            ->setNameID(new NameID($session->getNameId(), $session->getNameIdFormat()))
            ->setDestination($destination)
            ->setID(\LightSaml\Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setIssuer(new Issuer($destination));

        $context->getOutboundContext()->setMessage($request);
    }
}
