<?php

namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Helper;

/**
 * Sets the ID of the message in the outbound context
 */
class MessageIdAction extends AbstractProfileAction
{
    protected function doExecute(ProfileContext $context)
    {
        $id = Helper::generateID();
        MessageContextHelper::asSamlMessage($context->getOutboundContext())
            ->setId($id);

        $this->logger->info(
            sprintf('Message ID set to "%s"', $id),
            LogHelper::getActionContext($context, $this, array('message_id' => $id))
        );
    }
}
