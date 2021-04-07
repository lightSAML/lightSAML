<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;

class DestinationValidatorResponseAction extends AbstractDestinationValidatorAction
{
    /**
     * @param string $location
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
