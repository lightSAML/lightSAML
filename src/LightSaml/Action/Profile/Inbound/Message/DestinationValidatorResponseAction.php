<?php

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;

class DestinationValidatorResponseAction extends DestinationValidatorBaseAction
{
    protected function getCriteriaSet(ProfileContext $context)
    {
        $result = parent::getCriteriaSet($context);

        $result->add(new ServiceTypeCriteria(AssertionConsumerService::class));

        return $result;
    }
}
