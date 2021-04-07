<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Protocol;

use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class AuthnRequest extends AbstractRequest
{
    //region Attributes

    /** @var bool|null */
    protected $forceAuthn;

    /** @var bool|null */
    protected $isPassive;

    /** @var int|null */
    protected $assertionConsumerServiceIndex;

    /** @var string|null */
    protected $assertionConsumerServiceURL;

    /** @var int|null */
    protected $attributeConsumingServiceIndex;

    /** @var string|null */
    protected $protocolBinding;

    /** @var string|null */
    protected $providerName;

    //endregion

    //region Elements

    /** @var Conditions|null */
    protected $conditions;

    /** @var NameIDPolicy|null */
    protected $nameIDPolicy;

    /** @var Subject|null */
    protected $subject;

    /**
     * @param Subject|null $subject
     *
     * @return AuthnRequest
     */
    public function setSubject(Subject $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return Subject|null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string|null $providerName
     *
     * @return AuthnRequest
     */
    public function setProviderName($providerName)
    {
        $this->providerName = (string) $providerName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @param string|null $protocolBinding
     *
     * @return AuthnRequest
     */
    public function setProtocolBinding($protocolBinding)
    {
        $this->protocolBinding = (string) $protocolBinding;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProtocolBinding()
    {
        return $this->protocolBinding;
    }

    /**
     * @param NameIDPolicy|null $nameIDPolicy
     *
     * @return AuthnRequest
     */
    public function setNameIDPolicy(NameIDPolicy $nameIDPolicy)
    {
        $this->nameIDPolicy = $nameIDPolicy;

        return $this;
    }

    /**
     * @return NameIDPolicy|null
     */
    public function getNameIDPolicy()
    {
        return $this->nameIDPolicy;
    }

    /**
     * @param bool|null $isPassive
     *
     * @return AuthnRequest
     */
    public function setIsPassive($isPassive)
    {
        $this->isPassive = 0 == strcasecmp($isPassive, 'true') || true === $isPassive || 1 == $isPassive;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPassive()
    {
        return $this->isPassive;
    }

    /**
     * @return string|null
     */
    public function getIsPassiveString()
    {
        if (null === $this->isPassive) {
            return null;
        }

        return $this->isPassive ? 'true' : 'false';
    }

    /**
     * @param bool|null $forceAuthn
     *
     * @return AuthnRequest
     */
    public function setForceAuthn($forceAuthn)
    {
        $this->forceAuthn = 0 == strcasecmp($forceAuthn, 'true') || true === $forceAuthn || 1 == $forceAuthn;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getForceAuthn()
    {
        return $this->forceAuthn;
    }

    /**
     * @return string|null
     */
    public function getForceAuthnString()
    {
        if (null === $this->forceAuthn) {
            return null;
        }

        return $this->forceAuthn ? 'true' : 'false';
    }

    /**
     * @param Conditions|null $conditions
     *
     * @return AuthnRequest
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * @return \LightSaml\Model\Assertion\Conditions|null
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param int|null $attributeConsumingServiceIndex
     *
     * @return AuthnRequest
     */
    public function setAttributeConsumingServiceIndex($attributeConsumingServiceIndex)
    {
        $this->attributeConsumingServiceIndex = null !== $attributeConsumingServiceIndex
            ? intval(((string) $attributeConsumingServiceIndex))
            : null;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAttributeConsumingServiceIndex()
    {
        return $this->attributeConsumingServiceIndex;
    }

    /**
     * @param string|null $assertionConsumerServiceURL
     *
     * @return AuthnRequest
     */
    public function setAssertionConsumerServiceURL($assertionConsumerServiceURL)
    {
        $this->assertionConsumerServiceURL = (string) $assertionConsumerServiceURL;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAssertionConsumerServiceURL()
    {
        return $this->assertionConsumerServiceURL;
    }

    /**
     * @param int|null $assertionConsumerServiceIndex
     *
     * @return AuthnRequest
     */
    public function setAssertionConsumerServiceIndex($assertionConsumerServiceIndex)
    {
        $this->assertionConsumerServiceIndex = null !== $assertionConsumerServiceIndex
            ? intval((string) $assertionConsumerServiceIndex)
            : null;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAssertionConsumerServiceIndex()
    {
        return $this->assertionConsumerServiceIndex;
    }

    //endregion

    /**
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('AuthnRequest', SamlConstants::NS_PROTOCOL, $parent, $context);

        parent::serialize($result, $context);

        $this->attributesToXml([
                'ForceAuthn', 'IsPassive', 'ProtocolBinding', 'AssertionConsumerServiceIndex',
                'AssertionConsumerServiceURL', 'AttributeConsumingServiceIndex', 'ProviderName',
            ], $result);

        $this->singleElementsToXml(['Subject', 'NameIDPolicy', 'Conditions'], $result, $context);

        // must be last in order signature to include them all
        $this->singleElementsToXml(['Signature'], $result, $context);
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'AuthnRequest', SamlConstants::NS_PROTOCOL);

        parent::deserialize($node, $context);

        $this->attributesFromXml($node, [
            'ForceAuthn', 'IsPassive', 'ProtocolBinding', 'AssertionConsumerServiceIndex',
            'AssertionConsumerServiceURL', 'AttributeConsumingServiceIndex', 'ProviderName',
        ]);

        $this->singleElementsFromXml($node, $context, [
            'Subject' => ['saml', 'LightSaml\Model\Assertion\Subject'],
            'NameIDPolicy' => ['samlp', 'LightSaml\Model\Protocol\NameIDPolicy'],
            'Conditions' => ['saml', 'LightSaml\Model\Assertion\Conditions'],
        ]);
    }
}
