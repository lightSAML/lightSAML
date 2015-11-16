<?php

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\AssertionValidatorAction;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Tests\TestHelper;

class AssertionValidatorActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_and_assertion_validator()
    {
        new AssertionValidatorAction(TestHelper::getLoggerMock($this), TestHelper::getAssertionValidatorMock($this));
    }

    public function test_calls_assertion_validator_with_assertion_from_context()
    {
        $action = new AssertionValidatorAction(
            TestHelper::getLoggerMock($this),
            $assertionValidatorMock = TestHelper::getAssertionValidatorMock($this)
        );

        $context = TestHelper::getAssertionContext($assertion = new Assertion());

        $assertionValidatorMock->expects($this->once())
            ->method('validateAssertion')
            ->with($assertion);

        $action->execute($context);
    }
}
