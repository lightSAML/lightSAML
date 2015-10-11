<?php

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;

class DestinationValidatorResponseAction extends AbstractDestinationValidatorAction
{
    /**
     * @param ProfileContext $context
     * @param string         $location
     *
     * @return CriteriaSet
     */
    protected function getCriteriaSet(ProfileContext $context, $location)
    {
        $result = parent::getCriteriaSet($context, $location);

        $result->add(new ServiceTypeCriteria(AssertionConsumerService::class));

        return $result;
    }
}
