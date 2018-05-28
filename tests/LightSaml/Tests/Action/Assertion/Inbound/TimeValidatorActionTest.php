<?php

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\TimeValidatorAction;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Tests\BaseTestCase;

class TimeValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_validator_time_provider_and_allowed_skew()
    {
        new TimeValidatorAction($this->getLoggerMock(), $this->getAssertionTimeValidatorMock(), $this->getTimeProviderMock(), 120);
        $this->assertTrue(true);
    }

    public function test_calls_validator()
    {
        $action = new TimeValidatorAction(
            $loggerMock = $this->getLoggerMock(),
            $validatorMock = $this->getAssertionTimeValidatorMock(),
            $timeProviderMock = $this->getTimeProviderMock(),
            $allowedSkew = 120
        );

        $context = $this->getAssertionContext($assertion = new Assertion());

        $timeProviderMock->expects($this->once())
            ->method('getTimestamp')
            ->willReturn($timestamp = 123123123);

        $validatorMock->expects($this->once())
            ->method('validateTimeRestrictions')
            ->with($assertion, $timestamp, $allowedSkew);

        $action->execute($context);
    }
}
