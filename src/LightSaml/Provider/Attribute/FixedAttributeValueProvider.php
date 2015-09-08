<?php

namespace LightSaml\Provider\Attribute;

use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Model\Assertion\Attribute;

class FixedAttributeValueProvider implements AttributeValueProviderInterface
{
    /** @var Attribute[] */
    protected $attributes = array();

    /**
     * @param Attribute $attribute
     *
     * @return FixedAttributeValueProvider
     */
    public function add(Attribute $attribute)
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * @param AssertionContext $context
     *
     * @return Attribute[]
     */
    public function getValues(AssertionContext $context)
    {
        return $this->attributes;
    }
}
