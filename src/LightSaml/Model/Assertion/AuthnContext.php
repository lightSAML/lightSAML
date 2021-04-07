<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Assertion;

use LightSaml\Model\AbstractSamlModel;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class AuthnContext extends AbstractSamlModel
{
    /**
     * @var string|null
     */
    protected $authnContextClassRef;

    /**
     * @var string|null
     */
    protected $authnContextDecl;

    /**
     * @var string|null
     */
    protected $authnContextDeclRef;

    /**
     * @var string|null
     */
    protected $authenticatingAuthority;

    /**
     * @param string|null $authenticatingAuthority
     *
     * @return AuthnContext
     */
    public function setAuthenticatingAuthority($authenticatingAuthority)
    {
        $this->authenticatingAuthority = (string) $authenticatingAuthority;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticatingAuthority()
    {
        return $this->authenticatingAuthority;
    }

    /**
     * @param string|null $authnContextClassRef
     *
     * @return AuthnContext
     */
    public function setAuthnContextClassRef($authnContextClassRef)
    {
        $this->authnContextClassRef = (string) $authnContextClassRef;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthnContextClassRef()
    {
        return $this->authnContextClassRef;
    }

    /**
     * @param string|null $authnContextDecl
     *
     * @return AuthnContext
     */
    public function setAuthnContextDecl($authnContextDecl)
    {
        $this->authnContextDecl = (string) $authnContextDecl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthnContextDecl()
    {
        return $this->authnContextDecl;
    }

    /**
     * @param string|null $authnContextDeclRef
     *
     * @return AuthnContext
     */
    public function setAuthnContextDeclRef($authnContextDeclRef)
    {
        $this->authnContextDeclRef = (string) $authnContextDeclRef;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthnContextDeclRef()
    {
        return $this->authnContextDeclRef;
    }

    /**
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('AuthnContext', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->singleElementsToXml(
            ['AuthnContextClassRef', 'AuthnContextDecl', 'AuthnContextDeclRef', 'AuthenticatingAuthority'],
            $result,
            $context,
            SamlConstants::NS_ASSERTION
        );
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'AuthnContext', SamlConstants::NS_ASSERTION);

        $this->singleElementsFromXml($node, $context, [
            'AuthnContextClassRef' => ['saml', null],
            'AuthnContextDecl' => ['saml', null],
            'AuthnContextDeclRef' => ['saml', null],
            'AuthenticatingAuthority' => ['saml', null],
        ]);
    }
}
