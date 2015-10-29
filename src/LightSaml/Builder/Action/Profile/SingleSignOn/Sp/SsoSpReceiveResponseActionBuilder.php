<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Builder\Action\Profile\SingleSignOn\Sp;

use LightSaml\Action\Profile\FlushRequestStatesAction;
use LightSaml\Action\Profile\Inbound\Message\AssertBindingTypeAction;
use LightSaml\Action\Profile\Inbound\Message\DestinationValidatorResponseAction;
use LightSaml\Action\Profile\Inbound\Message\EntityIdFromMessageIssuerAction;
use LightSaml\Action\Profile\Inbound\Message\ResolvePartyEntityIdAction;
use LightSaml\Action\Profile\Inbound\Message\ReceiveMessageAction;
use LightSaml\Action\Profile\Inbound\Message\MessageSignatureValidatorAction;
use LightSaml\Action\Profile\Inbound\Response\AssertionAction;
use LightSaml\Action\Profile\Inbound\Response\DecryptAssertionsAction;
use LightSaml\Action\Profile\Inbound\Response\HasAssertionsValidatorAction;
use LightSaml\Action\Profile\Inbound\Response\HasAuthnStatementValidatorAction;
use LightSaml\Action\Profile\Inbound\Response\HasBearerAssertionsValidatorAction;
use LightSaml\Action\Profile\Inbound\Message\IssuerValidatorAction;
use LightSaml\Action\Profile\Inbound\Response\SpSsoStateAction;
use LightSaml\Action\Profile\Inbound\StatusResponse\InResponseToValidatorAction;
use LightSaml\Action\Profile\Inbound\StatusResponse\StatusAction;
use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Builder\Action\ActionBuilderInterface;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\SamlConstants;

class SsoSpReceiveResponseActionBuilder extends AbstractProfileActionBuilder
{
    /** @var ActionBuilderInterface */
    private $assertionActionBuilder;

    /**
     * @param BuildContainerInterface $buildContainer
     * @param ActionBuilderInterface  $assertionActionBuilder
     */
    public function __construct(BuildContainerInterface $buildContainer, ActionBuilderInterface $assertionActionBuilder)
    {
        parent::__construct($buildContainer);

        $this->assertionActionBuilder = $assertionActionBuilder;
    }

    /**
     * @return void
     */
    protected function doInitialize()
    {
        // Receive
        $this->add(new ReceiveMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ), 100);
        $this->add(new AssertBindingTypeAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            array(SamlConstants::BINDING_SAML2_HTTP_POST)
        ));

        // Response validation
        $this->add(new IssuerValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getNameIdValidator(),
            SamlConstants::NAME_ID_FORMAT_ENTITY
        ), 200);
        $this->add(new EntityIdFromMessageIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new ResolvePartyEntityIdAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getPartyContainer()->getSpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getIdpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getTrustOptionsStore()
        ));
        $this->add(new InResponseToValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getStoreContainer()->getRequestStateStore()
        ));
        $this->add(new StatusAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new DestinationValidatorResponseAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ));
        $this->add(new MessageSignatureValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSignatureValidator()
        ));

        $this->add(new DecryptAssertionsAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getCredentialResolver()
        ));

        $this->add(new HasAssertionsValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new HasAuthnStatementValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new HasBearerAssertionsValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));

        $this->add(new AssertionAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->assertionActionBuilder->build()
        ));

        $this->add(new FlushRequestStatesAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getStoreContainer()->getRequestStateStore()
        ));

        $this->add(new SpSsoStateAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSessionProcessor()
        ));
    }
}
