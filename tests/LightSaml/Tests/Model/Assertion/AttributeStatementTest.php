<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Model\Assertion;

use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Tests\BaseTestCase;

class AttributeStatementTest extends BaseTestCase
{
    public function test_get_first_attribute_by_name_returns_attribute()
    {
        $attributeStatement = new AttributeStatement();
        $attributeStatement->addAttribute(new Attribute('a'));
        $attributeStatement->addAttribute($expected = new Attribute('b'));
        $attributeStatement->addAttribute(new Attribute('b'));
        $attributeStatement->addAttribute(new Attribute('c'));

        $this->assertSame($expected, $attributeStatement->getFirstAttributeByName('b'));
    }

    public function test_get_first_attribute_by_name_returns_null()
    {
        $attributeStatement = new AttributeStatement();
        $attributeStatement->addAttribute(new Attribute('a'));
        $attributeStatement->addAttribute(new Attribute('b'));
        $attributeStatement->addAttribute(new Attribute('c'));

        $this->assertNull($attributeStatement->getFirstAttributeByName('x'));
    }
}
