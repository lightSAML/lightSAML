<?php

namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;

class ResolveEndpointSpAcsAction extends ResolveEndpointBaseAction
{
    protected function getServiceType(ProfileContext $context)
    {
        return AssertionConsumerService::class;
    }
}
