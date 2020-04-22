<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Action;

use LightSaml\Action\ActionInterface;
use LightSaml\Action\ActionLogWrapper;
use LightSaml\Tests\BaseTestCase;

class ActionLogWrapperTest extends BaseTestCase
{
    public function test__builds_loggable_action_with_given_logger()
    {
        $context = $this->getContextMock();

        $action = $this->getActionMock();
        $action->expects($this->once())
            ->method('execute')
            ->with($context);

        $loggerMock = $this->getLoggerMock();
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
     * @return \PHPUnit\Framework\MockObject\MockObject|ActionInterface
     */
    private function getActionMock()
    {
        return $this->getMockBuilder(ActionInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Context\ContextInterface
     */
    private function getContextMock()
    {
        return $this->getMockBuilder(\LightSaml\Context\ContextInterface::class)->getMock();
    }
}
