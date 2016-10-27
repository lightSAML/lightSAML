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
use LightSaml\Context\Profile\ProfileContext;

class ForwardRelayStateAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        if (null == $context->getInboundContext()->getMessage()) {
            return;
        }

        if ($context->getInboundMessage()->getRelayState()) {
            $this->logger->debug(sprintf('Forwarding relay state from inbound message: "%s"', $context->getInboundMessage()->getRelayState()));
            $context->getOutboundMessage()->setRelayState(
                $context->getInboundMessage()->getRelayState()
            );
        }
    }
}
