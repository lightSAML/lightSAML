<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Provider\Attribute;

use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Provider\Attribute\AttributeValueProviderInterface;
use LightSaml\Provider\Attribute\FixedAttributeValueProvider;
use LightSaml\Tests\BaseTestCase;

class FixedAttributeValueProviderTest extends BaseTestCase
{
    public function test_implements_attribute_value_provider_interface()
    {
        $this->assertInstanceOf(AttributeValueProviderInterface::class, new FixedAttributeValueProvider());
    }

    public function test_returns_added_attributes()
    {
        $provider = new FixedAttributeValueProvider();
        $provider->add($attribute1 = new Attribute());
        $provider->add($attribute2 = new Attribute());

        $arr = $provider->getValues(new AssertionContext());

        $this->assertCount(2, $arr);
        $this->assertSame($attribute1, $arr[0]);
        $this->assertSame($attribute2, $arr[1]);
    }
}
