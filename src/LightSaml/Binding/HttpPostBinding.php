<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Binding;

use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Error\LightSamlBindingException;
use LightSaml\Model\Protocol\AbstractRequest;
use LightSaml\Model\Protocol\SamlMessage;
use Symfony\Component\HttpFoundation\Request;

class HttpPostBinding extends AbstractBinding
{
    /**
     * @param MessageContext $context
     * @param null|string    $destination
     *
     * @return SamlPostResponse
     */
    public function send(MessageContext $context, $destination = null)
    {
        $message = MessageContextHelper::asSamlMessage($context);
        $destination = $message->getDestination() ? $message->getDestination() : $destination;

        $serializationContext = $context->getSerializationContext();
        $message->serialize($serializationContext->getDocument(), $serializationContext);
        $msgStr = $serializationContext->getDocument()->saveXML();

        $this->dispatchSend($msgStr);

        $msgStr = base64_encode($msgStr);

        $type = $message instanceof AbstractRequest ? 'SAMLRequest' : 'SAMLResponse';

        $data = array($type => $msgStr);
        if ($message->getRelayState()) {
            $data['RelayState'] = $message->getRelayState();
        }

        $result = new SamlPostResponse($destination, $data);
        $result->renderContent();

        return $result;
    }

    /**
     * @param Request        $request
     * @param MessageContext $context
     */
    public function receive(Request $request, MessageContext $context)
    {
        $post = $request->request->all();
        if (array_key_exists('SAMLRequest', $post)) {
            $msg = $post['SAMLRequest'];
        } elseif (array_key_exists('SAMLResponse', $post)) {
            $msg = $post['SAMLResponse'];
        } else {
            throw new LightSamlBindingException('Missing SAMLRequest or SAMLResponse parameter');
        }

        $msg = base64_decode($msg);

        $this->dispatchReceive($msg);

        $deserializationContext = $context->getDeserializationContext();
        $result = SamlMessage::fromXML($msg, $deserializationContext);

        if (array_key_exists('RelayState', $post)) {
            $result->setRelayState($post['RelayState']);
        }

        $context->setMessage($result);
    }
}
