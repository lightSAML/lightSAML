<?php

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
