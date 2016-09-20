<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Validator\Model\Assertion;

use LightSaml\Error\LightSamlValidationException;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\AudienceRestriction;
use LightSaml\Model\Assertion\AuthnStatement;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\OneTimeUse;
use LightSaml\Model\Assertion\ProxyRestriction;
use LightSaml\SamlConstants;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;
use LightSaml\Validator\Model\Statement\StatementValidatorInterface;
use LightSaml\Validator\Model\Subject\SubjectValidatorInterface;

class AssertionValidator implements AssertionValidatorInterface
{
    /** @var NameIdValidatorInterface */
    protected $nameIdValidator;

    /** @var SubjectValidatorInterface */
    protected $subjectValidator;

    /** @var StatementValidatorInterface */
    protected $statementValidator;

    /**
     * @param NameIdValidatorInterface    $nameIdValidator
     * @param SubjectValidatorInterface   $subjectValidator
     * @param StatementValidatorInterface $statementValidator
     */
    public function __construct(
        NameIdValidatorInterface $nameIdValidator,
        SubjectValidatorInterface $subjectValidator,
        StatementValidatorInterface $statementValidator
    ) {
        $this->nameIdValidator = $nameIdValidator;
        $this->subjectValidator = $subjectValidator;
        $this->statementValidator = $statementValidator;
    }

    /**
     * @param Assertion $assertion
     *
     * @return void
     */
    public function validateAssertion(Assertion $assertion)
    {
        $this->validateAssertionAttributes($assertion);
        $this->validateSubject($assertion);
        $this->validateConditions($assertion);
        $this->validateStatements($assertion);
    }

    /**
     * @param Assertion $assertion
     *
     * @throws LightSamlValidationException
     */
    protected function validateAssertionAttributes(Assertion $assertion)
    {
        if (false == Helper::validateRequiredString($assertion->getVersion())) {
            throw new LightSamlValidationException('Assertion element must have the Version attribute set.');
        }
        if ($assertion->getVersion() != SamlConstants::VERSION_20) {
            throw new LightSamlValidationException('Assertion element must have the Version attribute value equal to 2.0.');
        }
        if (false == Helper::validateRequiredString($assertion->getId())) {
            throw new LightSamlValidationException('Assertion element must have the ID attribute set.');
        }
        if (false == Helper::validateIdString($assertion->getId())) {
            throw new LightSamlValidationException('Assertion element must have an ID attribute with at least 16 characters (the equivalent of 128 bits).');
        }
        if (false == $assertion->getIssueInstantTimestamp()) {
            throw new LightSamlValidationException('Assertion element must have the IssueInstant attribute set.');
        }
        if (false == $assertion->getIssuer()) {
            throw new LightSamlValidationException('Assertion element must have an issuer element.');
        }
        $this->nameIdValidator->validateNameId($assertion->getIssuer());
    }

    /**
     * @param Assertion $assertion
     *
     * @throws LightSamlValidationException
     */
    protected function validateSubject(Assertion $assertion)
    {
        if (false == $assertion->getSubject()) {
            if (false == $assertion->getAllItems()) {
                throw new LightSamlValidationException('Assertion with no Statements must have a subject.');
            }
            foreach ($assertion->getAllItems() as $item) {
                if ($item instanceof AuthnStatement || $item instanceof AttributeStatement) {
                    throw new LightSamlValidationException('AuthnStatement, AuthzDecisionStatement and AttributeStatement require a subject.');
                }
            }
        } else {
            $this->subjectValidator->validateSubject($assertion->getSubject());
        }
    }

    protected function validateConditions(Assertion $assertion)
    {
        if (false == $assertion->getConditions()) {
            return;
        }

        $this->validateConditionsInterval($assertion->getConditions());

        $oneTimeUseSeen = $proxyRestrictionSeen = false;

        foreach ($assertion->getConditions()->getAllItems() as $item) {
            if ($item instanceof OneTimeUse) {
                if ($oneTimeUseSeen) {
                    throw new LightSamlValidationException('Assertion contained more than one condition of type OneTimeUse');
                }
                $oneTimeUseSeen = true;
            } elseif ($item instanceof ProxyRestriction) {
                if ($proxyRestrictionSeen) {
                    throw new LightSamlValidationException('Assertion contained more than one condition of type ProxyRestriction');
                }
                $proxyRestrictionSeen = true;

                $this->validateProxyRestriction($item);
            } elseif ($item instanceof AudienceRestriction) {
                $this->validateAudienceRestriction($item);
            }
        }
    }

    protected function validateConditionsInterval(Conditions $conditions)
    {
        if ($conditions->getNotBeforeTimestamp() &&
            $conditions->getNotOnOrAfterTimestamp() &&
            $conditions->getNotBeforeTimestamp() > $conditions->getNotOnOrAfterTimestamp()
        ) {
            throw new LightSamlValidationException('Conditions NotBefore MUST BE less than NotOnOrAfter');
        }
    }

    /**
     * @param ProxyRestriction $item
     *
     * @throws LightSamlValidationException
     */
    protected function validateProxyRestriction(ProxyRestriction $item)
    {
        if (null === $item->getCount() || '' === $item->getCount() || intval($item->getCount()) != $item->getCount() || $item->getCount() < 0) {
            throw new LightSamlValidationException('Count attribute of ProxyRestriction MUST BE a non-negative integer');
        }

        if ($item->getAllAudience()) {
            foreach ($item->getAllAudience() as $audience) {
                if (false == Helper::validateWellFormedUriString($audience)) {
                    throw new LightSamlValidationException('ProxyRestriction Audience MUST BE a wellformed uri');
                }
            }
        }
    }

    /**
     * @param AudienceRestriction $item
     *
     * @throws LightSamlValidationException
     */
    protected function validateAudienceRestriction(AudienceRestriction $item)
    {
        if (false == $item->getAllAudience()) {
            return;
        }

        foreach ($item->getAllAudience() as $audience) {
            if (false == Helper::validateWellFormedUriString($audience)) {
                throw new LightSamlValidationException('AudienceRestriction MUST BE a wellformed uri');
            }
        }
    }

    /**
     * @param Assertion $assertion
     */
    protected function validateStatements(Assertion $assertion)
    {
        if (false == $assertion->getAllItems()) {
            return;
        }

        foreach ($assertion->getAllItems() as $statement) {
            $this->statementValidator->validateStatement($statement);
        }
    }
}
