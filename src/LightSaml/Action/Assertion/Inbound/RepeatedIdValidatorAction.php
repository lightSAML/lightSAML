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
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Error\LightSamlContextException;
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
    /** @var IdStoreInterface */
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
            $this->validateBearerAssertion($context);
        }
    }

    /**
     * @param AssertionContext $context
     *
     * @throws \LightSaml\Error\LightSamlContextException
     */
    protected function validateBearerAssertion(AssertionContext $context)
    {
        if (null == $context->getAssertion()->getId()) {
            $message = 'Bearer Assertion must have ID attribute';
            $this->logger->error($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        if (null == $context->getAssertion()->getIssuer()) {
            $message = 'Bearer Assertion must have Issuer element';
            $this->logger->error($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        if ($this->idStore->has($context->getAssertion()->getIssuer()->getValue(), $context->getAssertion()->getId())) {
            $message = sprintf(
                "Repeated assertion id '%s' of issuer '%s'",
                $context->getAssertion()->getId(),
                $context->getAssertion()->getIssuer()->getValue()
            );
            $this->logger->error($message, LogHelper::getActionErrorContext($context, $this, [
                'id' => $context->getAssertion()->getId(),
                'issuer' => $context->getAssertion()->getIssuer()->getValue(),
            ]));
            throw new LightSamlContextException($context, $message);
        }

        $this->idStore->set(
            $context->getAssertion()->getIssuer()->getValue(),
            $context->getAssertion()->getId(),
            $this->getIdExpiryTime($context)
        );
    }

    /**
     * @param AssertionContext $context
     *
     * @throws \LogicException
     * @throws \LightSaml\Error\LightSamlValidationException
     *
     * @return \DateTime
     */
    protected function getIdExpiryTime(AssertionContext $context)
    {
        /** @var \DateTime $result */
        $result = null;
        $bearerConfirmations = $context->getAssertion()->getSubject()->getBearerConfirmations();
        if (null == $bearerConfirmations) {
            throw new \LogicException('Bearer assertion must have bearer subject confirmations');
        }

        foreach ($bearerConfirmations as $subjectConfirmation) {
            if (null == $subjectConfirmation->getSubjectConfirmationData()) {
                $message = 'Bearer SubjectConfirmation must have SubjectConfirmationData element';
                $this->logger->error($message, LogHelper::getActionErrorContext($context, $this));
                throw new LightSamlContextException($context, $message);
            }

            $dt = $subjectConfirmation->getSubjectConfirmationData()->getNotOnOrAfterDateTime();
            if (null == $dt) {
                $message = 'Bearer SubjectConfirmation must have NotOnOrAfter attribute';
                $this->logger->error($message, LogHelper::getActionErrorContext($context, $this));
                throw new LightSamlContextException($context, $message);
            }

            if (null == $result || $result->getTimestamp() < $dt->getTimestamp()) {
                $result = $dt;
            }
        }

        if (null == $result) {
            $message = 'Unable to find NotOnOrAfter attribute in bearer assertion';
            $this->logger->error($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        return $result;
    }
}
