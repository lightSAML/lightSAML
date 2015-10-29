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

/**
 * Sets destination of the outbound message to the value of location of endpoint from the context.
 */
class DestinationAction extends AbstractProfileAction
{
    /**
     * @param ProfileContext $context
     *
     * @return void
     */
    protected function doExecute(ProfileContext $context)
    {
        $endpoint = $context->getEndpoint();

        MessageContextHelper::asSamlMessage($context->getOutboundContext())
            ->setDestination($endpoint->getLocation());

        $this->logger->debug(
            sprintf('Destination set to "%s"', $endpoint->getLocation()),
            LogHelper::getActionContext($context, $this)
        );
    }
}
