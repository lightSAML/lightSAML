<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;

class EntityIdFromMessageIssuerAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $message = MessageContextHelper::asSamlMessage($context->getInboundContext());
        if (null == $message->getIssuer()) {
            throw new LightSamlContextException($context, 'Inbound messages does not have Issuer');
        }

        $context->getPartyEntityContext()->setEntityId($message->getIssuer()->getValue());
    }
}
