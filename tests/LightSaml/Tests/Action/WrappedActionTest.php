<?php

namespace LightSaml\Tests\Action;

use LightSaml\Action\ActionInterface;
use LightSaml\Action\WrappedAction;
use LightSaml\Tests\BaseTestCase;

class WrappedActionTest extends BaseTestCase
{
    public function test__before_and_after_called()
    {
        $context = $this->getContextMock();

        /** @var ActionInterface|\PHPUnit_Framework_MockObject_MockObject $action */
        $action = $this->getMockBuilder(ActionInterface::class)->getMock();
        /** @var WrappedAction|\PHPUnit_Framework_MockObject_MockObject $wrapper */
        $wrapper = $this->getMockForAbstractClass(WrappedAction::class, array($action));

        $beforeCalled = false;
        $executeCalled = false;
        $afterCalled = false;

        $wrapper->expects($this->once())
            ->method('beforeAction')
            ->with($context)
            ->willReturnCallback(function () use (&$beforeCalled, &$executeCalled, &$afterCalled) {
                $this->assertFalse($beforeCalled, 'beforeAction already called - should be called only once');
                $this->assertFalse($executeCalled, 'execute should not been executed before beforeAction');
                $this->assertFalse($afterCalled, 'afterAction should be executed before beforeAction');
                $beforeCalled = true;
            });

        $action->expects($this->once())
            ->method('execute')
            ->with($context)
            ->willReturnCallback(function () use (&$beforeCalled, &$executeCalled, &$afterCalled) {
                $this->assertTrue($beforeCalled, 'beforeAction should have been called');
                $this->assertFalse($executeCalled, 'execute already called - should be executed only once');
                $this->assertFalse($afterCalled, 'afterAction should be executed before beforeAction');
                $executeCalled = true;
            });

        $wrapper->expects($this->once())
            ->method('afterAction')
            ->with($context)
            ->willReturnCallback(function () use (&$beforeCalled, &$executeCalled, &$afterCalled) {
                $this->assertTrue($beforeCalled, 'beforeAction should have been called');
                $this->assertTrue($executeCalled, 'execute should be executed before afterAction');
                $this->assertFalse($afterCalled, 'afterAction already called - should be executed only once');
                $afterCalled = true;
            });

        $wrapper->execute($context);

        $this->assertTrue($beforeCalled);
        $this->assertTrue($executeCalled);
        $this->assertTrue($afterCalled);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Context\ContextInterface
     */
    private function getContextMock()
    {
        return $this->getMockBuilder(\LightSaml\Context\ContextInterface::class)->getMock();
    }
}
