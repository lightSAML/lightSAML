<?php

namespace LightSaml\Tests\Action;

use LightSaml\Action\ActionInterface;
use LightSaml\Action\ActionLogWrapper;
use Psr\Log\LoggerInterface;

class ActionLogWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function test__builds_loggable_action_with_given_logger()
    {
        $context = $this->getContextMock();

        $action = $this->getActionMock();
        $action->expects($this->once())
            ->method('execute')
            ->with($context);

        $loggerMock  = $this->getLoggerMock();
        $loggerMock->expects($this->once())
            ->method('debug')
            ->willReturnCallback(function ($pMessage, $pContext) use ($action, $context) {
                $expectedMessage = sprintf('Executing action "%s"', get_class($action));
                $this->assertEquals($expectedMessage, $pMessage);
                $this->assertArrayHasKey('context', $pContext);
                $this->assertArrayHasKey('action', $pContext);
                $this->assertSame($action, $pContext['action']);
                $this->assertSame($context, $pContext['context']);
            });

        $wrapper = new ActionLogWrapper($loggerMock);

        $wrappedAction = $wrapper->wrap($action);

        $wrappedAction->execute($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface
     */
    private function getLoggerMock()
    {
        return $this->getMock(LoggerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface
     */
    private function getActionMock()
    {
        return $this->getMock(ActionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Context\ContextInterface
     */
    private function getContextMock()
    {
        return $this->getMock('LightSaml\Context\ContextInterface');
    }
}
