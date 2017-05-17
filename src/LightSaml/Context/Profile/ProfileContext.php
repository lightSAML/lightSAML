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

use LightSaml\Error\LightSamlContextException;

class ProfileContext extends AbstractProfileContext
{
    const ROLE_SP = 'sp';
    const ROLE_IDP = 'idp';
    const ROLE_NONE = 'none';

    /** @var string */
    private $profileId;

    /** @var string */
    private $ownRole;

    /** @var string */
    private $relayState;

    /**
     * @param string $profileId
     * @param string $ownRole
     */
    public function __construct($profileId, $ownRole)
    {
        $this->profileId = $profileId;
        $this->ownRole = $ownRole;
    }

    /**
     * @return string
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return string
     */
    public function getOwnRole()
    {
        return $this->ownRole;
    }

    /**
     * @return string
     */
    public function getRelayState()
    {
        return $this->relayState;
    }

    /**
     * @param string $relayState
     *
     * @return ProfileContext
     */
    public function setRelayState($relayState)
    {
        $this->relayState = $relayState;

        return $this;
    }

    /**
     * @return MessageContext
     */
    public function getInboundContext()
    {
        return $this->getSubContext(ProfileContexts::INBOUND_MESSAGE, 'LightSaml\Context\Profile\MessageContext');
    }

    /**
     * @return MessageContext
     */
    public function getOutboundContext()
    {
        return $this->getSubContext(ProfileContexts::OUTBOUND_MESSAGE, 'LightSaml\Context\Profile\MessageContext');
    }

    /**
     * @return HttpRequestContext
     */
    public function getHttpRequestContext()
    {
        return $this->getSubContext(ProfileContexts::HTTP_REQUEST, 'LightSaml\Context\Profile\HttpRequestContext');
    }

    /**
     * @return HttpResponseContext
     */
    public function getHttpResponseContext()
    {
        return $this->getSubContext(ProfileContexts::HTTP_RESPONSE, 'LightSaml\Context\Profile\HttpResponseContext');
    }

    /**
     * @return EntityContext
     */
    public function getOwnEntityContext()
    {
        return $this->getSubContext(ProfileContexts::OWN_ENTITY, 'LightSaml\Context\Profile\EntityContext');
    }

    /**
     * @return EntityContext
     */
    public function getPartyEntityContext()
    {
        return $this->getSubContext(ProfileContexts::PARTY_ENTITY, 'LightSaml\Context\Profile\EntityContext');
    }

    /**
     * @return EndpointContext
     */
    public function getEndpointContext()
    {
        return $this->getSubContext(ProfileContexts::ENDPOINT, 'LightSaml\Context\Profile\EndpointContext');
    }

    /**
     * @return LogoutContext
     */
    public function getLogoutContext()
    {
        return $this->getSubContext(ProfileContexts::LOGOUT, 'LightSaml\Context\Profile\LogoutContext');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getHttpRequest()
    {
        $httpRequestContext = $this->getHttpRequestContext();
        if (null === $httpRequestContext->getRequest()) {
            throw new LightSamlContextException($this, 'Missing Request in HTTP request context');
        }

        return $httpRequestContext->getRequest();
    }

    /**
     * @return \LightSaml\Model\Protocol\SamlMessage
     */
    public function getInboundMessage()
    {
        $inboundContext = $this->getInboundContext();
        if (null === $inboundContext->getMessage()) {
            throw new LightSamlContextException($this, 'Missing message in inbound context');
        }

        return $inboundContext->getMessage();
    }

    /**
     * @return \LightSaml\Model\Protocol\SamlMessage
     */
    public function getOutboundMessage()
    {
        $outboundContext = $this->getOutboundContext();
        if (null === $outboundContext->getMessage()) {
            throw new LightSamlContextException($this, 'Missing message in outbound context');
        }

        return $outboundContext->getMessage();
    }

    /**
     * @return \LightSaml\Model\Metadata\Endpoint
     */
    public function getEndpoint()
    {
        $endpointContext = $this->getEndpointContext();
        if (null === $endpointContext->getEndpoint()) {
            throw new LightSamlContextException($this, 'Missing Endpoint in endpoint context');
        }

        return $endpointContext->getEndpoint();
    }

    /**
     * @return \LightSaml\Model\Metadata\EntityDescriptor
     */
    public function getOwnEntityDescriptor()
    {
        $ownEntityContext = $this->getOwnEntityContext();
        if (null === $ownEntityContext->getEntityDescriptor()) {
            throw new LightSamlContextException($this, 'Missing EntityDescriptor in own entity context');
        }

        return $ownEntityContext->getEntityDescriptor();
    }

    /**
     * @return \LightSaml\Model\Metadata\EntityDescriptor
     */
    public function getPartyEntityDescriptor()
    {
        $partyEntityContext = $this->getPartyEntityContext();
        if (null === $partyEntityContext->getEntityDescriptor()) {
            throw new LightSamlContextException($this, 'Missing EntityDescriptor in party entity context');
        }

        return $partyEntityContext->getEntityDescriptor();
    }

    /**
     * @return \LightSaml\Meta\TrustOptions\TrustOptions
     */
    public function getTrustOptions()
    {
        $partyEntityContext = $this->getPartyEntityContext();
        if (null === $partyEntityContext->getTrustOptions()) {
            throw new LightSamlContextException($this, 'Missing TrustOptions in party entity context');
        }

        return $partyEntityContext->getTrustOptions();
    }

    public function getLogoutSsoSessionState()
    {
        $logoutContext = $this->getLogoutContext();
        if (null == $logoutContext->getSsoSessionState()) {
            throw new LightSamlContextException($this, 'Missing SsoSessionState in logout context');
        }

        return $logoutContext->getSsoSessionState();
    }
}
