<?php

namespace LightSaml\Action\Profile\Outbound\AuthnRequest;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\AuthnRequest;

/**
 * Creates empty AuthnRequest in outbound context
 */
class CreateAuthnRequestAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $context->getOutboundContext()->setMessage(new AuthnRequest());
    }
}
