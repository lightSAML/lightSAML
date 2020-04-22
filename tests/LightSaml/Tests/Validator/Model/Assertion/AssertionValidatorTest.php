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
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Assertion\OneTimeUse;
use LightSaml\Model\Assertion\ProxyRestriction;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Validator\Model\Assertion\AssertionValidator;

class AssertionValidatorTest extends BaseTestCase
{
    public function test_must_have_version()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Assertion element must have the Version attribute set');

        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();
        $assertion->setVersion(null);

        $validator->validateAssertion($assertion);
    }

    public function test_must_have_version20()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Assertion element must have the Version attribute value equal to 2.0');

        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();
        $assertion->setVersion('1.0');

        $validator->validateAssertion($assertion);
    }

    public function test_must_have_id()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Assertion element must have the ID attribute set');

        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();

        $validator->validateAssertion($assertion);
    }

    public function test_must_have_valid_id()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Assertion element must have an ID attribute with at least 16 characters (the equivalent of 128 bits)');

        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();
        $assertion->setId('123456789012345');

        $validator->validateAssertion($assertion);
    }

    public function test_must_have_issue_instant()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Assertion element must have the IssueInstant attribute set');

        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456');

        $validator->validateAssertion($assertion);
    }

    public function test_must_have_issuer()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Assertion element must have an issuer element');

        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000);

        $validator->validateAssertion($assertion);
    }

    public function test_no_subject_no_statements_fails()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Assertion with no Statements must have a subject');

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'));

        $validator->validateAssertion($assertion);
    }

    public function test_authn_statement_requires_subject()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('AuthnStatement, AuthzDecisionStatement and AttributeStatement require a subject');

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->addItem(new AuthnStatement());

        $validator->validateAssertion($assertion);
    }

    public function test_attribute_statement_requires_subject()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('AuthnStatement, AuthzDecisionStatement and AttributeStatement require a subject');

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->addItem(new AttributeStatement());

        $validator->validateAssertion($assertion);
    }

    public function test_subject_validator_is_called()
    {
        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $subject = new Subject();

        $subjectValidatorMock->expects($this->once())
            ->method('validateSubject')
            ->with($subject);

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->setSubject($subject)
            ->addItem(new AttributeStatement());

        $validator->validateAssertion($assertion);
    }

    public function test_conditions_not_before_must_be_less_than_not_on_or_after()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Conditions NotBefore MUST BE less than NotOnOrAfter');

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->setSubject(new Subject())
            ->setConditions(
                (new Conditions())
                ->setNotBefore(1000)
                ->setNotOnOrAfter(999)
            );

        $validator->validateAssertion($assertion);
    }

    public function test_conditions_one_time_use_not_more_than_one()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Assertion contained more than one condition of type OneTimeUse');

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->setSubject(new Subject())
            ->setConditions(
                (new Conditions())
                ->addItem(new OneTimeUse())
                ->addItem(new OneTimeUse())
            );

        $validator->validateAssertion($assertion);
    }

    public function test_conditions_proxy_restriction_count_must_be_non_negative_integer()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Count attribute of ProxyRestriction MUST BE a non-negative integer');

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->setSubject(new Subject())
            ->setConditions(
                (new Conditions())
                    ->addItem(new ProxyRestriction())
            );

        $validator->validateAssertion($assertion);
    }

    public function test_conditions_proxy_restriction_audience_must_be_well_formed_uri_string()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('ProxyRestriction Audience MUST BE a wellformed uri');

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->setSubject(new Subject())
            ->setConditions(
                (new Conditions())
                    ->addItem(
                        new ProxyRestriction(1, ['not valid'])
                    )
            );

        $validator->validateAssertion($assertion);
    }

    public function test_conditions_proxy_restriction_not_more_than_one()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('Assertion contained more than one condition of type ProxyRestriction');

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->setSubject(new Subject())
            ->setConditions(
                (new Conditions())
                    ->addItem(new ProxyRestriction(1))
                    ->addItem(new ProxyRestriction(1))
            );

        $validator->validateAssertion($assertion);
    }

    public function test_conditions_audience_must_be_well_formed_uri_string()
    {
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $this->expectExceptionMessage('AudienceRestriction MUST BE a wellformed uri');

        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->setSubject(new Subject())
            ->setConditions(
                (new Conditions())
                    ->addItem(new AudienceRestriction('not valid'))
            );

        $validator->validateAssertion($assertion);
    }

    public function test_statement_validator_is_called_for_attribute_statement()
    {
        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $attributeStatement = new AttributeStatement();

        $statementValidatorMock->expects($this->once())
            ->method('validateStatement')
            ->with($attributeStatement);

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->setSubject(new Subject())
            ->addItem($attributeStatement);

        $validator->validateAssertion($assertion);
    }

    public function test_statement_validator_is_called_for_authn_statement()
    {
        $nameIdValidatorMock = $this->getNameIdValidatorMock();
        $subjectValidatorMock = $this->getSubjectValidatorMock();
        $statementValidatorMock = $this->getStatementValidatorMock();

        $validator = new AssertionValidator(
            $nameIdValidatorMock,
            $subjectValidatorMock,
            $statementValidatorMock
        );

        $authnStatement = new AuthnStatement();

        $statementValidatorMock->expects($this->once())
            ->method('validateStatement')
            ->with($authnStatement);

        $assertion = new Assertion();
        $assertion->setId('1234567890123456')
            ->setIssueInstant(1000)
            ->setIssuer(new Issuer('issuer'))
            ->setSubject(new Subject())
            ->addItem($authnStatement);

        $validator->validateAssertion($assertion);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Validator\Model\NameId\NameIdValidatorInterface
     */
    private function getNameIdValidatorMock()
    {
        return $this->getMockBuilder(\LightSaml\Validator\Model\NameId\NameIdValidatorInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Validator\Model\Statement\StatementValidatorInterface
     */
    private function getStatementValidatorMock()
    {
        return $this->getMockBuilder(\LightSaml\Validator\Model\Statement\StatementValidatorInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\LightSaml\Validator\Model\Subject\SubjectValidatorInterface
     */
    private function getSubjectValidatorMock()
    {
        return $this->getMockBuilder(\LightSaml\Validator\Model\Subject\SubjectValidatorInterface::class)->getMock();
    }
}
