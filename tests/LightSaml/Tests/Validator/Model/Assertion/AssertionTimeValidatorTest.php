<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Tests\Validator\Model\Assertion;

use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidator;

class AssertionTimeValidatorTest extends BaseTestCase
{
    public function test_conditions_not_before_fails()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Conditions.NotBefore must not be in the future');

        $now = 1000;

        $assertion = new Assertion();

        $assertion->setConditions((new Conditions())->setNotBefore(1100));

        $validator = new AssertionTimeValidator();

        $validator->validateTimeRestrictions($assertion, $now, 10);
    }

    public function test_conditions_not_on_or_after_fails()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Conditions.NotOnOrAfter must not be in the past');

        $now = 1000;

        $assertion = new Assertion();

        $assertion->setConditions((new Conditions())->setNotOnOrAfter(900));

        $validator = new AssertionTimeValidator();

        $validator->validateTimeRestrictions($assertion, $now, 10);
    }

    public function test_authn_statement_session_not_on_or_after_fails()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('AuthnStatement attribute SessionNotOnOrAfter MUST be in the future');

        $now = 1000;

        $assertion = new Assertion();

        $assertion->addItem((new AuthnStatement())->setSessionNotOnOrAfter(900));

        $validator = new AssertionTimeValidator();

        $validator->validateTimeRestrictions($assertion, $now, 10);
    }

    public function test_subject_not_before_fails()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('SubjectConfirmationData.NotBefore must not be in the future');

        $now = 1000;

        $assertion = new Assertion();

        $assertion->setSubject(
            (new Subject())->addSubjectConfirmation(
                (new SubjectConfirmation())->setSubjectConfirmationData(
                    (new SubjectConfirmationData())->setNotBefore(1100)
                )
            )
        );

        $validator = new AssertionTimeValidator();

        $validator->validateTimeRestrictions($assertion, $now, 10);
    }

    public function test_subject_not_on_or_after_fails()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('SubjectConfirmationData.NotOnOrAfter must not be in the past');

        $now = 1000;

        $assertion = new Assertion();

        $assertion->setSubject(
            (new Subject())->addSubjectConfirmation(
                (new SubjectConfirmation())->setSubjectConfirmationData(
                    (new SubjectConfirmationData())->setNotOnOrAfter(900)
                )
            )
        );

        $validator = new AssertionTimeValidator();

        $validator->validateTimeRestrictions($assertion, $now, 10);
    }

    public function test_pass()
    {
        $now = 1000;

        $assertion = new Assertion();

        $assertion->setSubject(
            (new Subject())->addSubjectConfirmation(
                (new SubjectConfirmation())->setSubjectConfirmationData(
                    (new SubjectConfirmationData())->setNotOnOrAfter(2000)
                )
            )
        );

        $assertion->addItem((new AuthnStatement())->setSessionNotOnOrAfter(2000));

        $assertion->setConditions((new Conditions())->setNotOnOrAfter(2000)->setNotBefore(900));

        $validator = new AssertionTimeValidator();

        $validator->validateTimeRestrictions($assertion, $now, 10);

        $this->assertTrue(true);
    }
}
