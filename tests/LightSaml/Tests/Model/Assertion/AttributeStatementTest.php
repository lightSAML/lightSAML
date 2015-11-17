<?php

namespace LightSaml\Tests\Model\Assertion;

use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;

class AttributeStatementTest extends \PHPUnit_Framework_TestCase
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
