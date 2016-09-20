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
use LightSaml\Context\Profile\ProfileContexts;
use LightSaml\Context\Profile\RequestStateContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Store\Request\RequestStateStoreInterface;
use Psr\Log\LoggerInterface;

class InResponseToValidatorAction extends AbstractAssertionAction
{
    /** @var RequestStateStoreInterface */
    protected $requestStore;

    /**
     * @param LoggerInterface            $logger
     * @param RequestStateStoreInterface $requestStore
     */
    public function __construct(LoggerInterface $logger, RequestStateStoreInterface $requestStore)
    {
        parent::__construct($logger);

        $this->requestStore = $requestStore;
    }

    /**
     * @param AssertionContext $context
     */
    protected function doExecute(AssertionContext $context)
    {
        if (null === $context->getAssertion()->getSubject()) {
            return;
        }

        foreach ($context->getAssertion()->getSubject()->getAllSubjectConfirmations() as $subjectConfirmation) {
            if ($subjectConfirmation->getSubjectConfirmationData() &&
                $subjectConfirmation->getSubjectConfirmationData()->getInResponseTo()
            ) {
                $requestState = $this->validateInResponseTo(
                    $subjectConfirmation->getSubjectConfirmationData()->getInResponseTo(),
                    $context
                );

                /** @var RequestStateContext $requestStateContext */
                $requestStateContext = $context->getSubContext(ProfileContexts::REQUEST_STATE, RequestStateContext::class);
                $requestStateContext->setRequestState($requestState);
            }
        }
    }

    /**
     * @param string           $inResponseTo
     * @param AssertionContext $context
     *
     * @return \LightSaml\State\Request\RequestState
     */
    protected function validateInResponseTo($inResponseTo, AssertionContext $context)
    {
        $requestState = $this->requestStore->get($inResponseTo);
        if (null == $requestState) {
            $message = sprintf("Unknown InResponseTo '%s'", $inResponseTo);
            $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        return $requestState;
    }
}
