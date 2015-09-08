<?php

namespace LightSaml\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlValidationException;

class HasAuthnStatementValidatorAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $response = MessageContextHelper::asResponse($context->getInboundContext());

        foreach ($response->getAllAssertions() as $assertion) {
            if ($assertion->getAllAuthnStatements()) {
                return;
            }
        }

        throw new LightSamlValidationException("Response must have at least one Assertion containing AuthStatement element");
    }
}
