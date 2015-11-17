<?php

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\TimeValidatorAction;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Tests\TestHelper;

class TimeValidatorActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_logger_validator_time_provider_and_allowed_skew()
    {
        new TimeValidatorAction(TestHelper::getLoggerMock($this), TestHelper::getAssertionTimeValidatorMock($this), TestHelper::getTimeProviderMock($this), 120);
    }

    public function test_calls_validator()
    {
        $action = new TimeValidatorAction(
            $loggerMock = TestHelper::getLoggerMock($this),
            $validatorMock = TestHelper::getAssertionTimeValidatorMock($this),
            $timeProviderMock = TestHelper::getTimeProviderMock($this),
            $allowedSkew = 120
        );

        $context = TestHelper::getAssertionContext($assertion = new Assertion());

        $timeProviderMock->expects($this->once())
            ->method('getTimestamp')
            ->willReturn($timestamp = 123123123);

        $validatorMock->expects($this->once())
            ->method('validateTimeRestrictions')
            ->with($assertion, $timestamp, $allowedSkew);

        $action->execute($context);
    }
}
