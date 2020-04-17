<?php

namespace LightSaml\Tests\Action;

use LightSaml\Action\ActionInterface;
use LightSaml\Action\CompositeAction;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Tests\Mock\Action\BarAction;
use LightSaml\Tests\Mock\Action\FooAction;

class CompositeActionTest extends BaseTestCase
{
    public function test__can_be_constructed_without_arguments()
    {
        $composite = new CompositeAction();
        $this->assertCount(0, $composite->getChildren());
    }

    public function test__can_be_constructed_with_array_of_actions()
    {
        $composite = new CompositeAction([$this->getActionMock(), $this->getActionMock()]);
        $this->assertCount(2, $composite->getChildren());
    }

    public function test__can_add_child_action()
    {
        $composite = new CompositeAction();
        $composite->add($this->getActionMock());
        $this->assertCount(1, $composite->getChildren());
    }

    public function test__execute_called_on_each_child()
    {
        $context = $this->getContextMock();

        $action1 = $this->getActionMock();
        $action1->expects($this->once())
            ->method('execute')
            ->with($context);

        $composite = new CompositeAction([$action1]);

        $action2 = $this->getActionMock();
        $action2->expects($this->once())
            ->method('execute')
            ->with($context);
        $composite->add($action2);

        $composite->execute($context);
    }

    public function test__map()
    {
        $action1 = $this->getActionMock();
        $action2 = $this->getActionMock();
        $action1mapped = $action2mapped = false;
        $action2replacement = null;

        $composite = new CompositeAction([$action1, $action2]);

        $composite->map(function (ActionInterface $action) use ($action1, $action2, &$action1mapped, &$action2mapped, &$action2replacement) {
            if ($action === $action1) {
                $action1mapped = true;

                return;
            } elseif ($action === $action2) {
                $action2mapped = true;
                $action2replacement = $this->getActionMock();

                return $action2replacement;
            } else {
                throw new \RuntimeException('Unexpected action given in map() method');
            }
        });

        $this->assertTrue($action1mapped);
        $this->assertTrue($action2mapped);

        $children = $composite->getChildren();
        $this->assertCount(2, $children);
        $this->assertSame($action1, $children[0]);
        $this->assertSame($action2replacement, $children[1]);
    }

    public function test_debug_tree()
    {
        $innerComposite = new CompositeAction([new FooAction(), new BarAction()]);
        $this->assertCount(2, $innerComposite->getChildren());
        $outerComposite = new CompositeAction([new FooAction(), $innerComposite, new BarAction()]);
        $this->assertCount(3, $outerComposite->getChildren());

        $actualTree = $outerComposite->debugPrintTree();

        $expectedTree = [
            CompositeAction::class => [
                FooAction::class => [],
                CompositeAction::class => [
                    FooAction::class => [],
                    BarAction::class => [],
                ],
                BarAction::class => [],
            ],
        ];

        $this->assertEquals($expectedTree, $actualTree);
    }

    public function test_to_string_returns_json_debug_tree_string()
    {
        $innerComposite = new CompositeAction([new FooAction(), new BarAction()]);
        $this->assertCount(2, $innerComposite->getChildren());
        $outerComposite = new CompositeAction([new FooAction(), $innerComposite, new BarAction()]);
        $this->assertCount(3, $outerComposite->getChildren());

        $actualValue = (string)$outerComposite;

        $expectedValue = <<<EOT
{
    "LightSaml\\\\Action\\\\CompositeAction": {
        "LightSaml\\\\Tests\\\\Mock\\\\Action\\\\FooAction": [],
        "LightSaml\\\\Action\\\\CompositeAction": {
            "LightSaml\\\\Tests\\\\Mock\\\\Action\\\\FooAction": [],
            "LightSaml\\\\Tests\\\\Mock\\\\Action\\\\BarAction": []
        },
        "LightSaml\\\\Tests\\\\Mock\\\\Action\\\\BarAction": []
    }
}
EOT;

        $this->assertJsonStringEqualsJsonString($expectedValue, $actualValue);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Action\ActionInterface
     */
    private function getActionMock()
    {
        return $this->getMockBuilder(\LightSaml\Action\ActionInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Context\ContextInterface
     */
    private function getContextMock()
    {
        return $this->getMockBuilder(\LightSaml\Context\ContextInterface::class)->getMock();
    }
}
