<?php

namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;

class ResolveEndpointIdpSsoAction extends ResolveEndpointBaseAction
{
    protected function getServiceType(ProfileContext $context)
    {
        return SingleSignOnService::class;
    }
}
