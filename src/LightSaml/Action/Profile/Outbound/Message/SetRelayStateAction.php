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

class SetRelayStateAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        if ($context->getRelayState()) {
            $this->logger->debug(
                sprintf('RelayState from context set to outbound message: "%s"', $context->getRelayState()),
                LogHelper::getActionContext($context, $this)
            );
            MessageContextHelper::asSamlMessage($context->getOutboundContext())
                ->setRelayState($context->getRelayState());
        }
    }
}
