<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Error\LightSamlValidationException;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Store\Id\IdStoreInterface;
use Psr\Log\LoggerInterface;

/**
 * 4.1.4.5  POST-Specific Processing Rules
 * The service provider MUST ensure that bearer assertions are not replayed, by maintaining the set of used
 * ID values for the length of time for which the assertion would be considered valid based on the
 * NotOnOrAfter attribute in the <SubjectConfirmationData>.
 */
class RepeatedIdValidatorAction extends AbstractAssertionAction
{
    /** @var  IdStoreInterface */
    protected $idStore;

    /**
     * @param LoggerInterface  $logger
     * @param IdStoreInterface $idStore
     */
    public function __construct(LoggerInterface $logger, IdStoreInterface $idStore)
    {
        parent::__construct($logger);

        $this->idStore = $idStore;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        if ($context->getAssertion()->hasBearerSubject()) {
            $this->validateBearerAssertion($context->getAssertion());
        }
    }

    /**
     * @param Assertion $assertion
     *
     * @throws \LightSaml\Error\LightSamlValidationException
     */
    protected function validateBearerAssertion(Assertion $assertion)
    {
        if (null == $assertion->getId()) {
            throw new LightSamlValidationException('Bearer Assertion must have ID attribute');
        }

        if (null == $assertion->getIssuer()) {
            throw new LightSamlValidationException('Bearer Assertion must have Issuer element');
        }

        if ($this->idStore->has($assertion->getIssuer()->getValue(), $assertion->getId())) {
            throw new LightSamlValidationException(sprintf(
                "Repeated assertion id '%s' of issuer '%s'",
                $assertion->getId(),
                $assertion->getIssuer()->getValue()
            ));
        }

        $this->idStore->set($assertion->getIssuer()->getValue(), $assertion->getId(), $this->getIdExpiryTime($assertion));
    }

    /**
     * @param Assertion $assertion
     *
     * @throws \LogicException
     * @throws \LightSaml\Error\LightSamlValidationException
     *
     * @return \DateTime
     */
    protected function getIdExpiryTime(Assertion $assertion)
    {
        /** @var \DateTime $result */
        $result = null;
        $bearerConfirmations = $assertion->getSubject()->getBearerConfirmations();
        if (null == $bearerConfirmations) {
            throw new \LogicException('Bearer assertion must have bearer subject confirmations');
        }

        foreach ($bearerConfirmations as $subjectConfirmation) {
            if (null == $subjectConfirmation->getSubjectConfirmationData()) {
                throw new LightSamlValidationException('Bearer SubjectConfirmation must have SubjectConfirmationData element');
            }

            $dt = $subjectConfirmation->getSubjectConfirmationData()->getNotOnOrAfterDateTime();
            if (null == $dt) {
                throw new LightSamlValidationException('Bearer SubjectConfirmation must have NotOnOrAfter attribute');
            }

            if (null == $result || $result->getTimestamp() < $dt->getTimestamp()) {
                $result = $dt;
            }
        }

        if (null == $result) {
            throw new LightSamlValidationException('Unable to find NotOnOrAfter attribute in bearer assertion');
        }

        return $result;
    }
}
