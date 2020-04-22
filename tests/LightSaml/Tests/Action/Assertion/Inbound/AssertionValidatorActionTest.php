<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\Inbound\AssertionValidatorAction;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Tests\BaseTestCase;

class AssertionValidatorActionTest extends BaseTestCase
{
    public function test_constructs_with_logger_and_assertion_validator()
    {
        new AssertionValidatorAction($this->getLoggerMock(), $this->getAssertionValidatorMock());
        $this->assertTrue(true);
    }

    public function test_calls_assertion_validator_with_assertion_from_context()
    {
        $action = new AssertionValidatorAction(
            $this->getLoggerMock(),
            $assertionValidatorMock = $this->getAssertionValidatorMock()
        );

        $context = $this->getAssertionContext($assertion = new Assertion());

        $assertionValidatorMock->expects($this->once())
            ->method('validateAssertion')
            ->with($assertion);

        $action->execute($context);
        $this->assertTrue(true);
    }
}
