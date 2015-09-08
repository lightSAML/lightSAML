<?php

namespace LightSaml\Action\Profile\Inbound\StatusResponse;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlAuthenticationException;
use LightSaml\Error\LightSamlContextException;

/**
 * Throws LightSamlAuthenticationException if status of inbound message is not successful
 */
class StatusAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $statusResponse = MessageContextHelper::asStatusResponse($context->getInboundContext());

        if ($statusResponse->getStatus() && $statusResponse->getStatus()->isSuccess()) {
            return;
        }

        if (null == $statusResponse->getStatus()) {
            $message = 'Status response does not have Status set';
            $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        $status = $statusResponse->getStatus()->getStatusCode()->getValue();
        $status .= "\n".$statusResponse->getStatus()->getStatusMessage();
        if ($statusResponse->getStatus()->getStatusCode()->getStatusCode()) {
            $status .= "\n".$statusResponse->getStatus()->getStatusCode()->getStatusCode()->getValue();
        }

        throw new LightSamlAuthenticationException($statusResponse, 'Unsuccessful SAML response: '.$status);
    }
}
