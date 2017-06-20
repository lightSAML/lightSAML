<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
        return'LightSaml\Model\Metadata\SingleLogoutService';
    }

    protected function getDescriptorType(ProfileContext $context)
    {
        $ssoSessionState = $context->getLogoutSsoSessionState();
        $ownEntityId = $context->getOwnEntityDescriptor()->getEntityID();

        if ($ssoSessionState->getIdpEntityId() == $ownEntityId) {
            return 'LightSaml\Model\Metadata\SpSsoDescriptor';
        } elseif ($ssoSessionState->getSpEntityId() == $ownEntityId) {
            return 'LightSaml\Model\Metadata\IdpSsoDescriptor';
        } else {
            throw new LightSamlContextException($context, 'Unable to resolve logout target descriptor type');
        }
    }
}
