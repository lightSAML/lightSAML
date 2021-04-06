<?php

namespace LightSaml\Tests\Context;

use LightSaml\Context\AbstractContext;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\EntityContext;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Context\Profile\RequestStateContext;
use LightSaml\Tests\BaseTestCase;

class AbstractContextTest extends BaseTestCase
{
    public function test_set_value_sets_parent()
    {
        $context = $this->getContextMock();
        $subContext = $this->getContextMock();

        $context->addSubContext('some', $subContext);
        $this->assertSame($context, $subContext->getParent());
        $this->assertEquals(1, $context->getIterator()->count());
    }

    public function test_remove_sets_parent_to_null()
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

    public function test_clear_sets_parent_to_null()
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

    public function test_get_sub_context_returns_set_context()
    {
        $context = $this->getContextMock();
        $subContext = $this->getContextMock();
        $context->addSubContext($name = 'some', $subContext);

        $this->assertSame($subContext, $context->getSubContext($name));
    }

    public function test_get_sub_context_returns_null_for_not_set_context()
    {
        $context = $this->getContextMock();
        $context->addSubContext('some', $this->getContextMock());

        $this->assertNull($context->getSubContext('other'));
    }

    public function test_add_sub_context_throws_if_not_a_context_value()
    {
        $this->expectExceptionMessage("Expected object or ContextInterface");
        $this->expectException(\InvalidArgumentException::class);
        $context = $this->getContextMock();
        $context->addSubContext($name = 'some', '123');
        $context->getSubContext($name);
    }

    public function test_get_sub_context_or_create_new_does_create_new_instance()
    {
        $context = $this->getContextMock();
        $value = $context->getSubContext($name = 'name', '\stdClass');

        $this->assertInstanceOf('\stdClass', $value);
    }

    public function test__created_sub_context_has_set_parent()
    {
        $context = $this->getContextMock();
        $subContext = $context->getSubContext($name = 'name', get_class($context));

        $this->assertSame($context, $subContext->getParent());
    }

    public function test_add_sub_context_returns_already_added_value()
    {
        $context = $this->getContextMock();

        $name = 'name';
        $context->addSubContext($name, $first = $this->getContextMock());
        $context->addSubContext($name, $first);

        $this->assertEquals(1, $context->getIterator()->count());
    }

    public function test_add_sub_context_sets_parent()
    {
        $context = $this->getContextMock();

        $context->addSubContext($name = 'name', $subContext = $this->getContextMock());

        $this->assertSame($context, $subContext->getParent());
    }

    public function test_add_sub_context_replaces_previous_value()
    {
        $context = $this->getContextMock();

        $name = 'name';
        $context->addSubContext($name, $first = $this->getContextMock());
        $context->addSubContext($name, $second = $this->getContextMock());

        $this->assertNull($first->getParent());
        $this->assertSame($context, $second->getParent());
        $this->assertSame($second, $context->getSubContext($name));
    }

    public function test_remove_sub_context_sets_parent_to_null()
    {
        $context = $this->getContextMock();

        $name = 'name';
        $context->addSubContext($name, $subContext = $this->getContextMock());
        $this->assertSame($context, $subContext->getParent());

        $context->removeSubContext($name);
        $this->assertNull($subContext->getParent());
        $this->assertNull($context->getSubContext($name));
    }

    public function test_contains_sub_context_returns_true_if_name_already_added()
    {
        $context = $this->getContextMock();

        $context->getSubContext($name = 'name', get_class($context));

        $this->assertTrue($context->containsSubContext($name));
    }

    public function test_contains_sub_context_returns_false_if_value_is_not_set()
    {
        $context = $this->getContextMock();
        $this->assertFalse($context->containsSubContext('name'));
    }

    public function test_get_path_string_returns_value()
    {
        $context = $this->getContextMock();
        $fooContext = $context->getSubContext('foo', get_class($context));
        $barContext = $fooContext->getSubContext('bar', get_class($context));
        $expectedValue = $barContext->getSubContext('value', get_class($context));

        $this->assertSame($expectedValue, $context->getPath('foo/bar/value'));
    }

    public function test_get_path_returns_null_for_non_existing_path()
    {
        $context = $this->getContextMock();
        $fooContext = $context->getSubContext('foo', get_class($context));
        $barContext = $fooContext->getSubContext('bar', get_class($context));
        $barContext->getSubContext('value', get_class($context));

        $this->assertNull($context->getPath('foo/non-existing/value'));
    }

    public function test_get_path_string_returns_null_for_too_deep_path()
    {
        $context = $this->getContextMock();
        $fooContext = $context->getSubContext('foo', get_class($context));
        $barContext = $fooContext->getSubContext('bar', get_class($context));
        $barContext->getSubContext('value', get_class($context));

        $this->assertNull($context->getPath('foo/bar/value/too-much'));
    }

    public function test_debug_tree()
    {
        $profileContext = $this->getProfileContext();
        $profileContext->getOwnEntityContext();
        $profileContext->getPartyEntityContext();
        $profileContext->addSubContext('assertion_01', $assertionSubContext01 = new AssertionContext());
        $assertionSubContext01->addSubContext('rs', new RequestStateContext());

        $actual = $profileContext->debugPrintTree();

        $expected = [
            'root' => ProfileContext::class,
            'root__children' => [
                'own_entity' => EntityContext::class,
                'party_entity' => EntityContext::class,
                'assertion_01' => AssertionContext::class,
                'assertion_01__children' => [
                    'rs' => RequestStateContext::class,
                ],
            ],
        ];

        $this->assertEquals($expected, $actual);
    }

    public function test_to_string_gives_debug_tree_string()
    {
        $profileContext = $this->getProfileContext();
        $profileContext->getOwnEntityContext();
        $profileContext->getPartyEntityContext();
        $profileContext->addSubContext('assertion_01', $assertionSubContext01 = new AssertionContext());
        $assertionSubContext01->addSubContext('rs', new RequestStateContext());

        $actual = (string)$profileContext;

        $expected = <<<EOT
{
    "root": "LightSaml\\\\Context\\\\Profile\\\\ProfileContext",
    "root__children": {
        "own_entity": "LightSaml\\\\Context\\\\Profile\\\\EntityContext",
        "party_entity": "LightSaml\\\\Context\\\\Profile\\\\EntityContext",
        "assertion_01": "LightSaml\\\\Context\\\\Profile\\\\AssertionContext",
        "assertion_01__children": {
            "rs": "LightSaml\\\\Context\\\\Profile\\\\RequestStateContext"
        }
    }
}
EOT;
        $this->assertJsonStringEqualsJsonString($expected, $actual);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractContext
     */
    private function getContextMock()
    {
        return $this->getMockForAbstractClass(AbstractContext::class);
    }
}
