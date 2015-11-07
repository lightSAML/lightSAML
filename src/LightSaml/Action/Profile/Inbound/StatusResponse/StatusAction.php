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
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlAuthenticationException;
use LightSaml\Error\LightSamlContextException;

/**
 * Throws LightSamlAuthenticationException if status of inbound message is not successful.
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
            $this->logger->error($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        $status = $statusResponse->getStatus()->getStatusCode()->getValue();
        $status .= "\n".$statusResponse->getStatus()->getStatusMessage();
        if ($statusResponse->getStatus()->getStatusCode()->getStatusCode()) {
            $status .= "\n".$statusResponse->getStatus()->getStatusCode()->getStatusCode()->getValue();
        }

        $message = 'Unsuccessful SAML response: '.$status;
        $this->logger->error($message, LogHelper::getActionErrorContext($context, $this, ['status' => $status]));
        throw new LightSamlAuthenticationException($statusResponse, $message);
    }
}
