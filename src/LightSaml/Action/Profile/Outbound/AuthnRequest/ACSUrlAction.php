<?php

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
