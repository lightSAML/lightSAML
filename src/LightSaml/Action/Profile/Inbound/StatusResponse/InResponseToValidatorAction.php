<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\StatusResponse;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\ProfileContexts;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Context\Profile\RequestStateContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\State\Request\RequestStateParameters;
use LightSaml\Store\Request\RequestStateStoreInterface;
use Psr\Log\LoggerInterface;

class InResponseToValidatorAction extends AbstractProfileAction
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
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $response = MessageContextHelper::asStatusResponse($context->getInboundContext());
        $inResponseTo = $response->getInResponseTo();
        if ($inResponseTo) {
            $requestState = $this->requestStore->get($inResponseTo);
            if (null == $requestState) {
                $message = sprintf("Unknown InResponseTo '%s'", $inResponseTo);
                $this->logger->critical($message, LogHelper::getActionErrorContext($context, $this, array(
                    'in_response_to' => $inResponseTo,
                )));
                throw new LightSamlContextException($context, $message);
            }
            $sentToParty = $requestState->getParameters()->get(RequestStateParameters::PARTY);
            if ($sentToParty && $response->getIssuer() && $response->getIssuer()->getValue() != $sentToParty) {
                $message = sprintf('AuthnRequest with id "%s" sent to party "%s" but StatusResponse for that request issued by party "%s"', $inResponseTo, $sentToParty, $response->getIssuer()->getValue());
                $this->logger->critical($message, LogHelper::getActionErrorContext($context, $this, array(
                    'sent_to' => $sentToParty,
                    'received_from' => $response->getIssuer()->getValue(),
                )));
                throw new LightSamlContextException($context, $message);
            }

            /** @var RequestStateContext $requestStateContext */
            $requestStateContext = $context->getInboundContext()->getSubContext(ProfileContexts::REQUEST_STATE, RequestStateContext::class);
            $requestStateContext->setRequestState($requestState);
        }
    }
}
