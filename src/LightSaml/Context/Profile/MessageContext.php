<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Context\Profile;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\LogoutRequest;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Protocol\SamlMessage;

class MessageContext extends AbstractProfileContext
{
    /** @var SamlMessage */
    private $message;

    /** @var string */
    private $bindingType;

    /**
     * @return string
     */
    public function getBindingType()
    {
        return $this->bindingType;
    }

    /**
     * @param string $bindingType
     *
     * @return MessageContext
     */
    public function setBindingType($bindingType)
    {
        $this->bindingType = $bindingType;

        return $this;
    }

    /**
     * @return SamlMessage|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param SamlMessage|null $message
     *
     * @return MessageContext
     */
    public function setMessage(SamlMessage $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return AuthnRequest|null
     */
    public function asAuthnRequest()
    {
        if ($this->message instanceof AuthnRequest) {
            return $this->message;
        }

        return null;
    }

    /**
     * @return LogoutRequest|null
     */
    public function asLogoutRequest()
    {
        if ($this->message instanceof LogoutRequest) {
            return $this->message;
        }

        return null;
    }

    /**
     * @return Response|null
     */
    public function asResponse()
    {
        if ($this->message instanceof Response) {
            return $this->message;
        }

        return null;
    }

    /**
     * @return LogoutResponse|null
     */
    public function asLogoutResponse()
    {
        if ($this->message instanceof LogoutResponse) {
            return $this->message;
        }

        return null;
    }

    /**
     * @return SerializationContext
     */
    public function getSerializationContext()
    {
        return $this->getSubContext(ProfileContexts::SERIALIZATION, SerializationContext::class);
    }

    /**
     * @return DeserializationContext
     */
    public function getDeserializationContext()
    {
        return $this->getSubContext(ProfileContexts::DESERIALIZATION, DeserializationContext::class);
    }
}
