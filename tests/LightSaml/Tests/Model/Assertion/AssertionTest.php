<?php

namespace LightSaml\Tests\Model\Assertion;

use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\Subject;

class AssertionTest extends \PHPUnit_Framework_TestCase
{
    public function equals_provider()
    {
        return [
            ['nameId', 'format', false, new Assertion()],
            ['nameId', 'format', false, (new Assertion())->setSubject(new Subject())],
            ['nameId', 'format', false, (new Assertion())->setSubject((new Subject())->setNameID(new NameID('nameId')))],
            ['nameId', 'format', true, (new Assertion())->setSubject((new Subject())->setNameID(new NameID('nameId', 'format')))],
            ['nameId', 'format', false, (new Assertion())->setSubject((new Subject())->setNameID(new NameID('other', 'format')))],
            ['nameId', 'format', false, (new Assertion())->setSubject((new Subject())->setNameID(new NameID('nameId', 'other')))],
        ];
    }

    /**
     * @dataProvider equals_provider
     */
    public function test_equals($nameId, $format, $expectedValue, Assertion $assertion)
    {
        $this->assertEquals($expectedValue, $assertion->equals($nameId, $format));
    }

    public function has_session_index_provider()
    {
        return [
            ['1111', false, new Assertion()],
            ['1111', false, (new Assertion())->addItem(new AuthnStatement())],
            ['1111', false, (new Assertion())->addItem((new AuthnStatement())->setSessionIndex('222'))],
            ['1111', true, (new Assertion())
                ->addItem((new AuthnStatement())->setSessionIndex('222'))
                ->addItem((new AuthnStatement())->setSessionIndex('1111'))
            ],
        ];
    }

    /**
     * @dataProvider has_session_index_provider
     */
    public function test_has_session_index($sessionIndex, $expectedValue, Assertion $assertion)
    {
        $this->assertEquals($expectedValue, $assertion->hasSessionIndex($sessionIndex));
    }

    public function has_any_session_index_provider()
    {
        return [
            [false, new Assertion()],
            [false, (new Assertion())->addItem(new AuthnStatement())],
            [true, (new Assertion())->addItem((new AuthnStatement())->setSessionIndex('123'))],
            [true, (new Assertion())
                ->addItem((new AuthnStatement())->setSessionIndex('111'))
                ->addItem((new AuthnStatement())->setSessionIndex('222'))
            ],
        ];
    }

    /**
     * @dataProvider has_any_session_index_provider
     */
    public function test_has_any_session_index($expectedValue, Assertion $assertion)
    {
        $this->assertEquals($expectedValue, $assertion->hasAnySessionIndex());
    }

    public function test_get_all_attribute_statements()
    {
        $assertion = new Assertion();
        $assertion->addItem(new AuthnStatement());
        $assertion->addItem($attributeStatement1 = new AttributeStatement());
        $assertion->addItem(new AuthnStatement());
        $assertion->addItem($attributeStatement2 = new AttributeStatement());

        $arr = $assertion->getAllAttributeStatements();

        $this->assertCount(2, $arr);
        $this->assertSame($attributeStatement1, $arr[0]);
        $this->assertSame($attributeStatement2, $arr[1]);
    }
}
