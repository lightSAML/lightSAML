<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Outbound\AuthnRequest;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\AuthnRequest;

/**
 * Creates empty AuthnRequest in outbound context.
 */
class CreateAuthnRequestAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $context->getOutboundContext()->setMessage(new AuthnRequest());
    }
}
