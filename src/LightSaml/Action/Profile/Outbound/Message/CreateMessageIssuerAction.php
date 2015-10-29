<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\SamlConstants;

/**
 * Sets the Issuer of the outbound message to the value of own entityID.
 */
class CreateMessageIssuerAction extends AbstractProfileAction
{
    /**
     * @param ProfileContext $context
     *
     * @return void
     */
    protected function doExecute(ProfileContext $context)
    {
        $ownEntityDescriptor = $context->getOwnEntityDescriptor();

        $issuer = new Issuer($ownEntityDescriptor->getEntityID());
        $issuer->setFormat(SamlConstants::NAME_ID_FORMAT_ENTITY);

        MessageContextHelper::asSamlMessage($context->getOutboundContext())
            ->setIssuer($issuer);

        $this->logger->debug(
            sprintf('Issuer set to "%s"', $ownEntityDescriptor->getEntityID()),
            LogHelper::getActionContext($context, $this)
        );
    }
}
