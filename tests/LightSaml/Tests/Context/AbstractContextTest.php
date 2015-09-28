<?php

namespace LightSaml\Tests\Context;

use LightSaml\Context\AbstractContext;

class AbstractContextTest extends \PHPUnit_Framework_TestCase
{
    public function testSetValueSetsParent()
    {
        $context = $this->getContextMock();
        $subContext = $this->getContextMock();

        $context->addSubContext('some', $subContext);
        $this->assertSame($context, $subContext->getParent());
        $this->assertEquals(1, $context->getIterator()->count());
    }

    public function testRemoveSetsParentToNull()
    {
        $context = $this->getContextMock();
        $subContext = $this->getContextMock();

        $context->addSubContext($name = 'some', $subContext);
        $this->assertSame($context, $subContext->getParent());
        $this->assertEquals(1, $context->getIterator()->count());

        $context->removeSubContext($name);
        $this->assertNull($subContext->getParent());
        $this->assertEquals(0, $context->getIterator()->count());
    }

    public function testClearSetsParentToNull()
    {
        $context = $this->getContextMock();
        $subContext1 = $this->getContextMock();
        $subContext2 = $this->getContextMock();

        $context->addSubContext($name1 = '111', $subContext1);
        $context->addSubContext($name2 = '222', $subContext2);
        $this->assertSame($context, $subContext1->getParent());
        $this->assertSame($context, $subContext2->getParent());
        $this->assertEquals(2, $context->getIterator()->count());

        $context->clearSubContexts();
        $this->assertNull($subContext1->getParent());
        $this->assertNull($subContext2->getParent());

        $this->assertEquals(0, $context->getIterator()->count());
    }

    public function testGetSubContextReturnsSetContext()
    {
        $context = $this->getContextMock();
        $subContext = $this->getContextMock();
        $context->addSubContext($name = 'some', $subContext);

        $this->assertSame($subContext, $context->getSubContext($name));
    }

    public function testGetSubContextReturnsNullForNotSetContext()
    {
        $context = $this->getContextMock();
        $context->addSubContext('some', $this->getContextMock());

        $this->assertNull($context->getSubContext('other'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected object or ContextInterface
     */
    public function testAddSubContextThrowsIfNotAContextValue()
    {
        $context = $this->getContextMock();
        $context->addSubContext($name = 'some', '123');
        $context->getSubContext($name);
    }

    public function testGetSubContextOrCreateNewDoesCreateNewInstance()
    {
        $context = $this->getContextMock();
        $value = $context->getSubContext($name = 'name', '\stdClass');

        $this->assertInstanceOf('\stdClass', $value);
    }

    public function testCreatedSubContextHasSetParent()
    {
        $context = $this->getContextMock();
        $subContext = $context->getSubContext($name = 'name', get_class($context));

        $this->assertSame($context, $subContext->getParent());
    }

    public function testAddSubContextReturnsAlreadyAddedValue()
    {
        $context = $this->getContextMock();

        $name = 'name';
        $context->addSubContext($name, $first = $this->getContextMock());
        $context->addSubContext($name, $first);

        $this->assertEquals(1, $context->getIterator()->count());
    }

    public function testAddSubContextSetsParent()
    {
        $context = $this->getContextMock();

        $context->addSubContext($name = 'name', $subContext = $this->getContextMock());

        $this->assertSame($context, $subContext->getParent());
    }

    public function testAddSubContextReplacesPreviousValue()
    {
        $context = $this->getContextMock();

        $name = 'name';
        $context->addSubContext($name, $first = $this->getContextMock());
        $context->addSubContext($name, $second = $this->getContextMock());

        $this->assertNull($first->getParent());
        $this->assertSame($context, $second->getParent());
        $this->assertSame($second, $context->getSubContext($name));
    }

    public function testRemoveSubContextSetsParentToNull()
    {
        $context = $this->getContextMock();

        $name = 'name';
        $context->addSubContext($name, $subContext = $this->getContextMock());
        $this->assertSame($context, $subContext->getParent());

        $context->removeSubContext($name);
        $this->assertNull($subContext->getParent());
        $this->assertNull($context->getSubContext($name));
    }

    public function testContainsSubContextReturnsTrueIfNameAlreadyAdded()
    {
        $context = $this->getContextMock();

        $context->getSubContext($name = 'name', get_class($context));

        $this->assertTrue($context->containsSubContext($name));
    }

    public function testContainsSubContextReturnsFalseIfValueIsNotSet()
    {
        $context = $this->getContextMock();
        $this->assertFalse($context->containsSubContext('name'));
    }

    public function testGetPathStringReturnsValue()
    {
        $context = $this->getContextMock();
        $fooContext = $context->getSubContext('foo', get_class($context));
        $barContext = $fooContext->getSubContext('bar', get_class($context));
        $expectedValue = $barContext->getSubContext('value', get_class($context));

        $this->assertSame($expectedValue, $context->getPath('foo/bar/value'));
    }

    public function testGetPathReturnsNullForNonExistingPath()
    {
        $context = $this->getContextMock();
        $fooContext = $context->getSubContext('foo', get_class($context));
        $barContext = $fooContext->getSubContext('bar', get_class($context));
        $barContext->getSubContext('value', get_class($context));

        $this->assertNull($context->getPath('foo/non-existing/value'));
    }

    public function testGetPathStringReturnsNullForTooDeepPath()
    {
        $context = $this->getContextMock();
        $fooContext = $context->getSubContext('foo', get_class($context));
        $barContext = $fooContext->getSubContext('bar', get_class($context));
        $barContext->getSubContext('value', get_class($context));

        $this->assertNull($context->getPath('foo/bar/value/too-much'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractContext
     */
    private function getContextMock()
    {
        return $this->getMockForAbstractClass(AbstractContext::class);
    }
}
