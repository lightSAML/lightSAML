<?php

namespace LightSaml\Tests\Action\Assertion;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\ContextInterface;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Tests\TestHelper;

class AbstractAssertionActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger()
    {
        $this->getAbstractAssertionActionMock([TestHelper::getLoggerMock($this)]);
    }

    public function test_do_execute_called_with_assertion_context()
    {
        $action = $this->getAbstractAssertionActionMock([TestHelper::getLoggerMock($this)]);

        $context = new AssertionContext();

        $action->expects($this->once())
            ->method('doExecute')
            ->with($context);

        $action->execute($context);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlContextException
     * @expectedExceptionMessage Expected AssertionContext
     */
    public function test_throws_context_exception_for_non_assertion_context()
    {
        $action = $this->getAbstractAssertionActionMock([TestHelper::getLoggerMock($this)]);
        $action->execute($this->getMock(ContextInterface::class));
    }

    /**
     * @param array $arguments
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractAssertionAction
     */
    private function getAbstractAssertionActionMock(array $arguments)
    {
        return $this->getMockForAbstractClass(AbstractAssertionAction::class, $arguments);
    }
}
