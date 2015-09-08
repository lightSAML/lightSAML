<?php

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Metadata\SingleSignOnService;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;

class DestinationValidatorAuthnRequestAction extends DestinationValidatorBaseAction
{
    protected function getCriteriaSet(ProfileContext $context)
    {
        $result = parent::getCriteriaSet($context);

        $result->add(new ServiceTypeCriteria(SingleSignOnService::class));

        return $result;
    }
}
