<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Action\Assertion;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\ContextInterface;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Tests\BaseTestCase;

class AbstractAssertionActionTest extends BaseTestCase
{
    public function test_constructs_with_logger()
    {
        $this->getAbstractAssertionActionMock([$this->getLoggerMock()]);
        $this->assertTrue(true);
    }

    public function test_do_execute_called_with_assertion_context()
    {
        $action = $this->getAbstractAssertionActionMock([$this->getLoggerMock()]);

        $context = new AssertionContext();

        $action->expects($this->once())
            ->method('doExecute')
            ->with($context);

        $action->execute($context);
    }

    public function test_throws_context_exception_for_non_assertion_context()
    {
        $this->expectException(\LightSaml\Error\LightSamlContextException::class);
        $this->expectExceptionMessage('Expected AssertionContext');

        $action = $this->getAbstractAssertionActionMock([$this->getLoggerMock()]);
        $action->execute($this->getMockBuilder(ContextInterface::class)->getMock());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|AbstractAssertionAction
     */
    private function getAbstractAssertionActionMock(array $arguments)
    {
        return $this->getMockForAbstractClass(AbstractAssertionAction::class, $arguments);
    }
}
