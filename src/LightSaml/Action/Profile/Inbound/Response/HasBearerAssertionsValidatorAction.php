<?php

namespace LightSaml\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlValidationException;

class HasBearerAssertionsValidatorAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $response = MessageContextHelper::asResponse($context->getInboundContext());

        if ($response->getBearerAssertions()) {
            return;
        }

        throw new LightSamlValidationException('Response must contain at least one bearer assertion');
    }
}
