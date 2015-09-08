<?php

namespace LightSaml\Provider\Attribute;

use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Model\Assertion\Attribute;

interface AttributeValueProviderInterface
{
    /**
     * @param AssertionContext $context
     *
     * @return Attribute[]
     */
    public function getValues(AssertionContext $context);
}
