<?php

namespace LightSaml\Tests\Validator\Model\Statement;

use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\SubjectLocality;
use LightSaml\SamlConstants;
use LightSaml\Tests\BaseTestCase;
use LightSaml\Validator\Model\Statement\StatementValidator;

class StatementValidatorTest extends BaseTestCase
{
    public function test_unsupported_statement_fails()
    {
        $this->expectExceptionMessageMatches("/Unsupported Statement type '\w+'/");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $statementMock = $this->getMockForAbstractClass('LightSaml\Model\Assertion\AbstractStatement');

        $validator = new StatementValidator();

        $validator->validateStatement($statementMock);
    }

    public function test_authn_statement_fails_with_out_authn_instant()
    {
        $this->expectExceptionMessage("AuthnStatement MUST have an AuthnInstant attribute");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $authnStatement = new AuthnStatement();

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    public function test_authn_statement_fails_with_session_index_empty_string()
    {
        $this->expectExceptionMessage("SessionIndex attribute of AuthnStatement must contain at least one non-whitespace character");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789)
            ->setSessionIndex('');

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    public function test_authn_statement_fails_with_subject_locality_address_empty_string()
    {
        $this->expectExceptionMessage("Address attribute of SubjectLocality must contain at least one non-whitespace character");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $subjectLocality = new SubjectLocality();
        $subjectLocality->setAddress('');

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789)
            ->setSubjectLocality($subjectLocality);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    public function test_authn_statement_fails_with_subject_locality_dns_name_empty_string()
    {
        $this->expectExceptionMessage("DNSName attribute of SubjectLocality must contain at least one non-whitespace character");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $subjectLocality = new SubjectLocality();
        $subjectLocality->setDNSName('');

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789)
            ->setSubjectLocality($subjectLocality);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    public function test_authn_statement_fails_with_out_authn_context()
    {
        $this->expectExceptionMessage("AuthnStatement MUST have an AuthnContext element");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    public function test_authn_statement_fails_with_empty_authn_context()
    {
        $this->expectExceptionMessage("AuthnContext element MUST contain at least one AuthnContextClassRef, AuthnContextDecl or AuthnContextDeclRef element");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $authnContext = new AuthnContext();

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);
        $authnStatement->setAuthnContext($authnContext);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    public function test_authn_statement_fails_with_authn_context_with_more_then_two_elements()
    {
        $this->expectExceptionMessage("AuthnContext MUST NOT contain more than two elements");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
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

    public function test_authn_context_class_ref_must_be_well_formed_uri_string()
    {
        $this->expectExceptionMessage("AuthnContextClassRef has a value which is not a wellformed absolute uri");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $authnContext = new AuthnContext();
        $authnContext->setAuthnContextClassRef('in valid');

        $authnStatement = new AuthnStatement();
        $authnStatement->setAuthnInstant(123456789);
        $authnStatement->setAuthnContext($authnContext);

        $validator = new StatementValidator();

        $validator->validateStatement($authnStatement);
    }

    public function test_authn_context_decl_ref_must_be_well_formed_uri_string()
    {
        $this->expectExceptionMessage("AuthnContextDeclRef has a value which is not a wellformed absolute uri");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
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

        $this->assertTrue(true);
    }

    public function test_empty_attribute_statement_fails()
    {
        $this->expectExceptionMessage("AttributeStatement MUST contain at least one Attribute or EncryptedAttribute");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
        $attributeStatement = new AttributeStatement();

        $validator = new StatementValidator();

        $validator->validateStatement($attributeStatement);
    }

    public function test_attribute_with_blank_name_fails()
    {
        $this->expectExceptionMessage("Name attribute of Attribute element MUST contain at least one non-whitespace character");
        $this->expectException(\LightSaml\Error\LightSamlValidationException::class);
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

        $this->assertTrue(true);
    }
}
