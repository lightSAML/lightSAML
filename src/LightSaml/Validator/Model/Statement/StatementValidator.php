<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Validator\Model\Statement;

use LightSaml\Error\LightSamlValidationException;
use LightSaml\Helper;
use LightSaml\Model\Assertion\AbstractStatement;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AuthnContext;
use LightSaml\Model\Assertion\AuthnStatement;

class StatementValidator implements StatementValidatorInterface
{
    /**
     * @param AbstractStatement $statement
     *
     * @throws \LightSaml\Error\LightSamlValidationException
     *
     * @return void
     */
    public function validateStatement(AbstractStatement $statement)
    {
        if ($statement instanceof AuthnStatement) {
            $this->validateAuthnStatement($statement);
        } elseif ($statement instanceof AttributeStatement) {
            $this->validateAttributeStatement($statement);
        } else {
            throw new LightSamlValidationException(sprintf("Unsupported Statement type '%s'", get_class($statement)));
        }
    }

    private function validateAuthnStatement(AuthnStatement $statement)
    {
        if (false == $statement->getAuthnInstantTimestamp()) {
            throw new LightSamlValidationException('AuthnStatement MUST have an AuthnInstant attribute');
        }
        if (false == Helper::validateOptionalString($statement->getSessionIndex())) {
            throw new LightSamlValidationException('SessionIndex attribute of AuthnStatement must contain at least one non-whitespace character');
        }
        if ($statement->getSubjectLocality()) {
            if (false == Helper::validateOptionalString($statement->getSubjectLocality()->getAddress())) {
                throw new LightSamlValidationException('Address attribute of SubjectLocality must contain at least one non-whitespace character');
            }
            if (false == Helper::validateOptionalString($statement->getSubjectLocality()->getDnsName())) {
                throw new LightSamlValidationException('DNSName attribute of SubjectLocality must contain at least one non-whitespace character');
            }
        }
        if (false == $statement->getAuthnContext()) {
            throw new LightSamlValidationException('AuthnStatement MUST have an AuthnContext element');
        }
        $this->validateAuthnContext($statement->getAuthnContext());
    }

    private function validateAuthnContext(AuthnContext $authnContext)
    {
        if (false == $authnContext->getAuthnContextClassRef() &&
            false == $authnContext->getAuthnContextDecl() &&
            false == $authnContext->getAuthnContextDeclRef()
        ) {
            throw new LightSamlValidationException('AuthnContext element MUST contain at least one AuthnContextClassRef, AuthnContextDecl or AuthnContextDeclRef element');
        }

        if ($authnContext->getAuthnContextClassRef() &&
            $authnContext->getAuthnContextDecl() &&
            $authnContext->getAuthnContextDeclRef()
        ) {
            throw new LightSamlValidationException('AuthnContext MUST NOT contain more than two elements.');
        }

        if ($authnContext->getAuthnContextClassRef()) {
            if (false == Helper::validateWellFormedUriString($authnContext->getAuthnContextClassRef())) {
                throw new LightSamlValidationException('AuthnContextClassRef has a value which is not a wellformed absolute uri');
            }
        }
        if ($authnContext->getAuthnContextDeclRef()) {
            if (false === Helper::validateWellFormedUriString($authnContext->getAuthnContextDeclRef())) {
                throw new LightSamlValidationException('AuthnContextDeclRef has a value which is not a wellformed absolute uri');
            }
        }
    }

    private function validateAttributeStatement(AttributeStatement $statement)
    {
        if (false == $statement->getAllAttributes()) {
            throw new LightSamlValidationException('AttributeStatement MUST contain at least one Attribute or EncryptedAttribute');
        }

        foreach ($statement->getAllAttributes() as $attribute) {
            $this->validateAttribute($attribute);
        }
    }

    /**
     * @param Attribute $attribute
     *
     * @throws LightSamlValidationException
     *
     * @return void
     */
    private function validateAttribute(Attribute $attribute)
    {
        if (false == Helper::validateRequiredString($attribute->getName())) {
            throw new LightSamlValidationException('Name attribute of Attribute element MUST contain at least one non-whitespace character');
        }
    }
}
