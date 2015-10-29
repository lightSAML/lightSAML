<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile\Helper;

use LightSaml\Context\Profile\MessageContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Model\Protocol\AbstractRequest;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\StatusResponse;

abstract class MessageContextHelper
{
    /**
     * @param MessageContext $context
     *
     * @return \LightSaml\Model\Protocol\SamlMessage
     */
    public static function asSamlMessage(MessageContext $context)
    {
        $message = $context->getMessage();
        if ($message) {
            return $message;
        }

        throw new LightSamlContextException($context, 'Missing SamlMessage');
    }
    /**
     * @param MessageContext $context
     *
     * @return \LightSaml\Model\Protocol\AuthnRequest
     */
    public static function asAuthnRequest(MessageContext $context)
    {
        $message = $context->getMessage();
        if ($message instanceof AuthnRequest) {
            return $message;
        }

        throw new LightSamlContextException($context, 'Expected AuthnRequest');
    }

    /**
     * @param MessageContext $context
     *
     * @return \LightSaml\Model\Protocol\AbstractRequest
     */
    public static function asAbstractRequest(MessageContext $context)
    {
        $message = $context->getMessage();
        if ($message instanceof AbstractRequest) {
            return $message;
        }

        throw new LightSamlContextException($context, 'Expected AbstractRequest');
    }

    /**
     * @param MessageContext $context
     *
     * @return \LightSaml\Model\Protocol\Response
     */
    public static function asResponse(MessageContext $context)
    {
        $message = $context->getMessage();
        if ($message instanceof Response) {
            return $message;
        }

        throw new LightSamlContextException($context, 'Expected Response');
    }

    /**
     * @param MessageContext $context
     *
     * @return \LightSaml\Model\Protocol\StatusResponse
     */
    public static function asStatusResponse(MessageContext $context)
    {
        $message = $context->getMessage();
        if ($message instanceof StatusResponse) {
            return $message;
        }

        throw new LightSamlContextException($context, 'Expected StatusResponse');
    }

    /**
     * @param MessageContext $context
     *
     * @return \LightSaml\Model\Protocol\LogoutRequest
     */
    public static function asLogoutRequest(MessageContext $context)
    {
        $message = $context->getMessage();
        if ($message instanceof LogoutRequest) {
            return $message;
        }

        throw new LightSamlContextException($context, 'Expected LogoutRequest');
    }

    /**
     * @param MessageContext $context
     *
     * @return \LightSaml\Model\Protocol\LogoutResponse
     */
    public static function asLogoutResponse(MessageContext $context)
    {
        $message = $context->getMessage();
        if ($message instanceof LogoutResponse) {
            return $message;
        }

        throw new LightSamlContextException($context, 'Expected LogoutResponse');
    }
}
