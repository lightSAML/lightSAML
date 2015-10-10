<?php

namespace LightSaml\Tests\Validator\Model\Assertion;

use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidator;

class AssertionTimeValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Conditions.NotBefore must not be in the future
     */
    public function test_conditions_not_before_fails()
    {
        $now = 1000;

        $assertion = new Assertion();

        $assertion->setConditions((new Conditions())->setNotBefore(1100));

        $validator = new AssertionTimeValidator();

        $validator->validateTimeRestrictions($assertion, $now, 10);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Conditions.NotOnOrAfter must not be in the past
     */
    public function test_conditions_not_on_or_after_fails()
    {
        $now = 1000;

        $assertion = new Assertion();

        $assertion->setConditions((new Conditions())->setNotOnOrAfter(900));

        $validator = new AssertionTimeValidator();

        $validator->validateTimeRestrictions($assertion, $now, 10);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnStatement attribute SessionNotOnOrAfter MUST be in the future
     */
    public function test_authn_statement_session_not_on_or_after_fails()
    {
        $now = 1000;

        $assertion = new Assertion();

        $assertion->addItem((new AuthnStatement())->setSessionNotOnOrAfter(900));

        $validator = new AssertionTimeValidator();

        $validator->validateTimeRestrictions($assertion, $now, 10);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage SubjectConfirmationData.NotBefore must not be in the future
     */
    public function test_subject_not_before_fails()
    {
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage SubjectConfirmationData.NotOnOrAfter must not be in the past
     */
    public function test_subject_not_on_or_after_fails()
    {
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
    }
}
