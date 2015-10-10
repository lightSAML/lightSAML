<?php

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
use LightSaml\Validator\Model\Assertion\AssertionValidator;

class AssertionValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Assertion element must have the Version attribute set
     */
    public function testMustHaveVersion()
    {
        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();
        $assertion->setVersion(null);

        $validator->validateAssertion($assertion);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Assertion element must have the Version attribute value equal to 2.0
     */
    public function testMustHaveVersion20()
    {
        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();
        $assertion->setVersion('1.0');

        $validator->validateAssertion($assertion);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Assertion element must have the ID attribute set
     */
    public function testMustHaveId()
    {
        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();

        $validator->validateAssertion($assertion);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Assertion element must have an ID attribute with at least 16 characters (the equivalent of 128 bits)
     */
    public function testMustHaveValidId()
    {
        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();
        $assertion->setId('123456789012345');

        $validator->validateAssertion($assertion);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Assertion element must have the IssueInstant attribute set
     */
    public function testMustHaveIssueInstant()
    {
        $validator = new AssertionValidator(
            $this->getNameIdValidatorMock(),
            $this->getSubjectValidatorMock(),
            $this->getStatementValidatorMock()
        );

        $assertion = new Assertion();
        $assertion->setId('1234567890123456');

        $validator->validateAssertion($assertion);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Assertion element must have an issuer element
     */
    public function testMustHaveIssuer()
    {
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Assertion with no Statements must have a subject
     */
    public function testNoSubjectNoStatementsFails()
    {
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnStatement, AuthzDecisionStatement and AttributeStatement require a subject
     */
    public function testAuthnStatementRequiresSubject()
    {
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnStatement, AuthzDecisionStatement and AttributeStatement require a subject
     */
    public function testAttributeStatementRequiresSubject()
    {
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

    public function testSubjectValidatorIsCalled()
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Conditions NotBefore MUST BE less than NotOnOrAfter
     */
    public function testConditionsNotBeforeMustBeLessThanNotOnOrAfter()
    {
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Assertion contained more than one condition of type OneTimeUse
     */
    public function testConditionsOneTimeUseNotMoreThanOne()
    {
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Count attribute of ProxyRestriction MUST BE a non-negative integer
     */
    public function testConditionsProxyRestrictionCountMustBeNonNegativeInteger()
    {
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage ProxyRestriction Audience MUST BE a wellformed uri
     */
    public function testConditionsProxyRestrictionAudienceMustBeWellFormedUriString()
    {
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Assertion contained more than one condition of type ProxyRestriction
     */
    public function testConditionsProxyRestrictionNotMoreThanOne()
    {
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

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AudienceRestriction MUST BE a wellformed uri
     */
    public function testConditionsAudienceMustBeWellFormedUriString()
    {
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

    public function testStatementValidatorIsCalledForAttributeStatement()
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

    public function testStatementValidatorIsCalledForAuthnStatement()
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
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Validator\Model\NameId\NameIdValidatorInterface
     */
    private function getNameIdValidatorMock()
    {
        return $this->getMock('LightSaml\Validator\Model\NameId\NameIdValidatorInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Validator\Model\Statement\StatementValidatorInterface
     */
    private function getStatementValidatorMock()
    {
        return $this->getMock('LightSaml\Validator\Model\Statement\StatementValidatorInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\LightSaml\Validator\Model\Subject\SubjectValidatorInterface
     */
    private function getSubjectValidatorMock()
    {
        return $this->getMock('LightSaml\Validator\Model\Subject\SubjectValidatorInterface');
    }
}
