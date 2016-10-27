<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
     * @param \LightSaml\Model\Assertion\Attribute[] $attributes
     *
     * @return FixedAttributeValueProvider
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = [];
        foreach ($attributes as $attribute) {
            $this->add($attribute);
        }

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
