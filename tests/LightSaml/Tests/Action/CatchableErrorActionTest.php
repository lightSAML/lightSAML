<?php

namespace LightSaml\Tests\Action;

use LightSaml\Action\ActionInterface;
use LightSaml\Action\CatchableErrorAction;
use LightSaml\Context\AbstractContext;
use LightSaml\Context\ContextInterface;
use LightSaml\Context\Profile\ExceptionContext;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Context\Profile\ProfileContexts;

class CatchableErrorActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_two_actions()
    {
        new CatchableErrorAction($this->getActionMock(), $this->getActionMock());
    }

    public function test_execute_calls_first_action()
    {
        $mainAction =  new CatchableErrorAction(
            $firstAction = $this->getActionMock(),
            $secondAction = $this->getActionMock()
        );
        $context = $this->getContextMock();
        $firstAction->expects($this->once())
            ->method('execute')
            ->with($context);
        $secondAction->expects($this->never())
            ->method('execute');

        $mainAction->execute($context);
    }

    public function test_execute_calls_second_action_if_first_throws_exception_and_add_exception_to_context()
    {
        $mainAction =  new CatchableErrorAction(
            $firstAction = $this->getActionMock(),
            $secondAction = $this->getActionMock()
        );
        $context = $this->getContextMock();
        $firstAction->expects($this->once())
            ->method('execute')
            ->with($context)
            ->willThrowException($exception = new \Exception());
        $secondAction->expects($this->once())
            ->method('execute')
            ->with($context)
        ;

        $mainAction->execute($context);

        /** @var ExceptionContext $exceptionContext */
        $exceptionContext = $context->getSubContext(ProfileContexts::EXCEPTION);
        $this->assertNotNull($exceptionContext);
        $this->assertInstanceOf(ExceptionContext::class, $exceptionContext);
        $this->assertSame($exception, $exceptionContext->getException());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Action\ActionInterface
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
        return $this->getMockForAbstractClass(AbstractContext::class);
    }
}
