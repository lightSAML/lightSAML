<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;

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

        $message = 'Response must have at least one Assertion containing AuthnStatement element';
        $this->logger->error($message, LogHelper::getActionErrorContext($context, $this));
        throw new LightSamlContextException($context, $message);
    }
}
