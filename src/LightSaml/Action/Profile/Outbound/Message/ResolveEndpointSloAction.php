<?php

namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\SingleLogoutService;
use LightSaml\Model\Metadata\SpSsoDescriptor;

class ResolveEndpointSloAction extends ResolveEndpointBaseAction
{
    protected function getServiceType(ProfileContext $context)
    {
        return SingleLogoutService::class;
    }

    protected function getDescriptorType(ProfileContext $context)
    {
        $ssoSessionState= $context->getLogoutSsoSessionState();
        $ownEntityId = $context->getOwnEntityDescriptor()->getEntityID();

        if ($ssoSessionState->getIdpEntityId() == $ownEntityId) {
            return SpSsoDescriptor::class;
        } elseif ($ssoSessionState->getSpEntityId() == $ownEntityId) {
            return IdpSsoDescriptor::class;
        } else {
            throw new LightSamlContextException($context, 'Unable to resolve logout target descriptor type');
        }
    }
}
