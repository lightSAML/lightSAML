<?php

namespace LightSaml\Tests\Builder\Action;

use LightSaml\Action\ActionInterface;
use LightSaml\Action\CompositeAction;
use LightSaml\Builder\Action\CompositeActionBuilder;
use LightSaml\Context\ContextInterface;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Tests\Mock\Action\FooAction;

class CompositeActionBuilderTest extends BaseTestCase
{
    public function test__throws_on_priority_true()
    {
        $this->expectException(\InvalidArgumentException::class);
        $compositeBuilder = new CompositeActionBuilder();
        $compositeBuilder->add(new FooAction(), true);
    }

    public function test__throws_on_priority_string()
    {
        $this->expectException(\InvalidArgumentException::class);
        $compositeBuilder = new CompositeActionBuilder();
        $compositeBuilder->add(new FooAction(), "asc");
    }

    public function test__ranked_as_added_with_out_priority_parameter()
    {
        $order = 1;
        $action1 = $this->getActionMock(1, $order);
        $action2 = $this->getActionMock(2, $order);
        $action3 = $this->getActionMock(3, $order);

        $compositeBuilder = new CompositeActionBuilder();
        $compositeBuilder->add($action1);
        $compositeBuilder->add($action2);
        $compositeBuilder->add($action3);

        $compositeAction = $compositeBuilder->build();

        $this->assertInstanceOf(CompositeAction::class, $compositeAction);

        $compositeAction->execute($this->getMockBuilder(ContextInterface::class)->getMock());
    }

    public function test__ranked_as_given_priority_parameter()
    {
        $order = 1;
        $action1 = $this->getActionMock(3, $order);
        $action2 = $this->getActionMock(1, $order);
        $action3 = $this->getActionMock(2, $order);

        $compositeBuilder = new CompositeActionBuilder();
        $compositeBuilder->add($action1, 10);
        $compositeBuilder->add($action2, 2);
        $compositeBuilder->add($action3, 7);

        $compositeAction = $compositeBuilder->build();

        $this->assertInstanceOf(CompositeAction::class, $compositeAction);

        $compositeAction->execute($this->getMockBuilder(ContextInterface::class)->getMock());
    }

    /**
     * @param int $expectedOrder
     * @param int $order
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface
     */
    private function getActionMock($expectedOrder, &$order)
    {
        $action = $this->getMockBuilder(ActionInterface::class)->getMock();
        $action->expects($this->once())
            ->method('execute')
            ->willReturnCallback(function () use ($expectedOrder, &$order) {
                $this->assertEquals($expectedOrder, $order);
                $order++;
            })
        ;

        return $action;
    }
}
