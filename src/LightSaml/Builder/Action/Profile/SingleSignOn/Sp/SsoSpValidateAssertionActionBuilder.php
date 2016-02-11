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

use LightSaml\Action\Assertion\Inbound\AssertionSignatureValidatorAction;
use LightSaml\Action\Assertion\Inbound\AssertionValidatorAction;
use LightSaml\Action\Assertion\Inbound\AssertionIssuerFormatValidatorAction;
use LightSaml\Action\Assertion\Inbound\InResponseToValidatorAction;
use LightSaml\Action\Assertion\Inbound\KnownAssertionIssuerAction;
use LightSaml\Action\Assertion\Inbound\RecipientValidatorAction;
use LightSaml\Action\Assertion\Inbound\RepeatedIdValidatorAction;
use LightSaml\Action\Assertion\Inbound\TimeValidatorAction;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\SamlConstants;

class SsoSpValidateAssertionActionBuilder extends AbstractProfileActionBuilder
{
    /**
     * @return void
     */
    protected function doInitialize()
    {
        $this->add(new AssertionValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getAssertionValidator()
        ), 100);
        $this->add(new AssertionIssuerFormatValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            SamlConstants::NAME_ID_FORMAT_ENTITY
        ));
        $this->add(new InResponseToValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getStoreContainer()->getRequestStateStore()
        ));
        $this->add(new KnownAssertionIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getPartyContainer()->getIdpEntityDescriptorStore()
        ));
        $this->add(new RecipientValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ));
        $this->add(new RepeatedIdValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getStoreContainer()->getIdStateStore()
        ));
        $this->add(new TimeValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getAssertionTimeValidator(),
            $this->buildContainer->getSystemContainer()->getTimeProvider(),
            120
        ));
        $this->add(new AssertionSignatureValidatorAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getSignatureValidator(),
            true
        ));
    }
}
