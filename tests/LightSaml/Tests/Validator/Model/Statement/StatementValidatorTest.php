<?php

namespace LightSaml\Tests\Validator\Model\Statement;

use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\SubjectLocality;
use LightSaml\SamlConstants;
use LightSaml\Validator\Model\Statement\StatementValidator;

class StatementValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessageRegExp /Unsupported Statement type '\w+'/
     */
    public function testUnsupportedStatementFails()
    {
        $statementMock = $this->getMockForAbstractClass('LightSaml\Model\Assertion\AbstractStatement');

        $validator = new StatementValidator();

        $validator->validateStatement($statementMock);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnStatement MUST have an AuthnInstant attribute
     */
    public function testAuthnStatementFailsWithOutAuthnInstant()
    {
        $authnStatement = new AuthnStatement();

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage SessionIndex attribute of AuthnStatement must contain at least one non-whitespace character
     */
    public function testAuthnStatementFailsWithSessionIndexEmptyString()
    {
        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789)
            ->setSessionIndex('');

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Address attribute of SubjectLocality must contain at least one non-whitespace character
     */
    public function testAuthnStatementFailsWithSubjectLocalityAddressEmptyString()
    {
        $subjectLocality = new SubjectLocality();
        $subjectLocality->setAddress('');

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789)
            ->setSubjectLocality($subjectLocality);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage DNSName attribute of SubjectLocality must contain at least one non-whitespace character
     */
    public function testAuthnStatementFailsWithSubjectLocalityDnsNameEmptyString()
    {
        $subjectLocality = new SubjectLocality();
        $subjectLocality->setDNSName('');

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789)
            ->setSubjectLocality($subjectLocality);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnStatement MUST have an AuthnContext element
     */
    public function testAuthnStatementFailsWithOutAuthnContext()
    {
        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnContext element MUST contain at least one AuthnContextClassRef, AuthnContextDecl or AuthnContextDeclRef element
     */
    public function testAuthnStatementFailsWithEmptyAuthnContext()
    {
        $authnContext = new AuthnContext();

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);
        $authnStatement->setAuthnContext($authnContext);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnContext MUST NOT contain more than two elements
     */
    public function testAuthnStatementFailsWithAuthnContextWithMoreThenTwoElements()
    {
        $authnContext = new AuthnContext();
        $authnContext->setAuthnContextClassRef('AuthnContextClassRef')
            ->setAuthnContextDecl('AuthnContextDecl')
            ->setAuthnContextDeclRef('AuthnContextDeclRef');

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);
        $authnStatement->setAuthnContext($authnContext);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnContextClassRef has a value which is not a wellformed absolute uri
     */
    public function testAuthnContextClassRefMustBeWellFormedUriString()
    {
        $authnContext = new AuthnContext();
        $authnContext->setAuthnContextClassRef('in valid');

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);
        $authnStatement->setAuthnContext($authnContext);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnContextDeclRef has a value which is not a wellformed absolute uri
     */
    public function testAuthnContextDeclRefMustBeWellFormedUriString()
    {
        $authnContext = new AuthnContext();
        $authnContext->setAuthnContextDeclRef('in valid');

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);
        $authnStatement->setAuthnContext($authnContext);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    public function testAuthnStatementOk()
    {
        $authnContext = new AuthnContext();
        $authnContext->setAuthnContextClassRef(SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT);

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);
        $authnStatement->setAuthnContext($authnContext);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AttributeStatement MUST contain at least one Attribute or EncryptedAttribute
     */
    public function testEmptyAttributeStatementFails()
    {
        $attributeStatement = new AttributeStatement();

        $validator = new StatementValidator();

        $validator->validateStatement($attributeStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Name attribute of Attribute element MUST contain at least one non-whitespace character
     */
    public function testAttributeWithBlankNameFails()
    {
        $attributeStatement = new AttributeStatement();
        $attributeStatement->addAttribute(new Attribute(' '));

        $validator = new StatementValidator();

        $validator->validateStatement($attributeStatement);
    }

    public function testAttributeStatementOk()
    {
        $attributeStatement = new AttributeStatement();
        $attributeStatement->addAttribute(new Attribute('name', 'value'));

        $validator = new StatementValidator();

        $validator->validateStatement($attributeStatement);
    }
}
