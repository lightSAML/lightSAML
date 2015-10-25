<?php

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

        $this->logger->debug(sprintf('Forwarding relay state: "%s"', $context->getInboundMessage()->getRelayState()));

        $context->getOutboundMessage()->setRelayState(
            $context->getInboundMessage()->getRelayState()
        );
    }
}
