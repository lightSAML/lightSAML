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
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;

class ACSUrlAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $ownEntityDescriptor = $context->getOwnEntityDescriptor();

        $ownSpSsoDescriptor = $ownEntityDescriptor->getFirstSpSsoDescriptor();
        if (null == $ownSpSsoDescriptor) {
            throw new LightSamlContextException($context, 'Missing own SP SSO Descriptor');
        }

        $acsService = $ownSpSsoDescriptor->getFirstAssertionConsumerService();
        if (null === $acsService) {
            throw new LightSamlContextException($context, 'Missing own ACS Service in SP SSO Descriptor');
        }

        MessageContextHelper::asAuthnRequest($context->getOutboundContext())
            ->setAssertionConsumerServiceURL($acsService->getLocation());
    }
}
