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
    public function test_unsupported_statement_fails()
    {
        $statementMock = $this->getMockForAbstractClass('LightSaml\Model\Assertion\AbstractStatement');

        $validator = new StatementValidator();

        $validator->validateStatement($statementMock);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage AuthnStatement MUST have an AuthnInstant attribute
     */
    public function test_authn_statement_fails_with_out_authn_instant()
    {
        $authnStatement = new AuthnStatement();

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage SessionIndex attribute of AuthnStatement must contain at least one non-whitespace character
     */
    public function test_authn_statement_fails_with_session_index_empty_string()
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
    public function test_authn_statement_fails_with_subject_locality_address_empty_string()
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
    public function test_authn_statement_fails_with_subject_locality_dns_name_empty_string()
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
    public function test_authn_statement_fails_with_out_authn_context()
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
    public function test_authn_statement_fails_with_empty_authn_context()
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
    public function test_authn_statement_fails_with_authn_context_with_more_then_two_elements()
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
    public function test_authn_context_class_ref_must_be_well_formed_uri_string()
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
    public function test_authn_context_decl_ref_must_be_well_formed_uri_string()
    {
        $authnContext = new AuthnContext();
        $authnContext->setAuthnContextDeclRef('in valid');

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);
        $authnStatement->setAuthnContext($authnContext);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    public function test_authn_statement_ok()
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
    public function test_empty_attribute_statement_fails()
    {
        $attributeStatement = new AttributeStatement();

        $validator = new StatementValidator();

        $validator->validateStatement($attributeStatement);
    }

    /**
     * @expectedException \LightSaml\Error\LightSamlValidationException
     * @expectedExceptionMessage Name attribute of Attribute element MUST contain at least one non-whitespace character
     */
    public function test_attribute_with_blank_name_fails()
    {
        $attributeStatement = new AttributeStatement();
        $attributeStatement->addAttribute(new Attribute(' '));

        $validator = new StatementValidator();

        $validator->validateStatement($attributeStatement);
    }

    public function test_attribute_statement_ok()
    {
        $attributeStatement = new AttributeStatement();
        $attributeStatement->addAttribute(new Attribute('name', 'value'));

        $validator = new StatementValidator();

        $validator->validateStatement($attributeStatement);
    }
}
