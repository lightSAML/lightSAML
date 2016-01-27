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

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Assertion\Conditions;
use LightSaml\Model\Assertion\Subject;
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
     * @param null|string $providerName
     *
     * @return AuthnRequest
     */
    public function setProviderName($providerName)
    {
        $this->providerName = (string) $providerName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @param null|string $protocolBinding
     *
     * @return AuthnRequest
     */
    public function setProtocolBinding($protocolBinding)
    {
        $this->protocolBinding = (string) $protocolBinding;

        return $this;
    }

    /**
     * @return null|string
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
        $this->isPassive = strcasecmp($isPassive, 'true') == 0 || $isPassive === true || $isPassive == 1;

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
        if ($this->isPassive === null) {
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
        $this->forceAuthn = strcasecmp($forceAuthn, 'true') == 0 || $forceAuthn === true || $forceAuthn == 1;

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
        if ($this->forceAuthn === null) {
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
     * @param null|int $attributeConsumingServiceIndex
     *
     * @return AuthnRequest
     */
    public function setAttributeConsumingServiceIndex($attributeConsumingServiceIndex)
    {
        $this->attributeConsumingServiceIndex = $attributeConsumingServiceIndex !== null
            ? intval(((string) $attributeConsumingServiceIndex))
            : null;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getAttributeConsumingServiceIndex()
    {
        return $this->attributeConsumingServiceIndex;
    }

    /**
     * @param null|string $assertionConsumerServiceURL
     *
     * @return AuthnRequest
     */
    public function setAssertionConsumerServiceURL($assertionConsumerServiceURL)
    {
        $this->assertionConsumerServiceURL = (string) $assertionConsumerServiceURL;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAssertionConsumerServiceURL()
    {
        return $this->assertionConsumerServiceURL;
    }

    /**
     * @param null|int $assertionConsumerServiceIndex
     *
     * @return AuthnRequest
     */
    public function setAssertionConsumerServiceIndex($assertionConsumerServiceIndex)
    {
        $this->assertionConsumerServiceIndex = $assertionConsumerServiceIndex !== null
            ? intval((string) $assertionConsumerServiceIndex)
            : null;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getAssertionConsumerServiceIndex()
    {
        return $this->assertionConsumerServiceIndex;
    }

    //endregion

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('AuthnRequest', SamlConstants::NS_PROTOCOL, $parent, $context);

        parent::serialize($result, $context);

        $this->attributesToXml(array(
                'ForceAuthn', 'IsPassive', 'ProtocolBinding', 'AssertionConsumerServiceIndex',
                'AssertionConsumerServiceURL', 'AttributeConsumingServiceIndex', 'ProviderName',
            ), $result);

        $this->singleElementsToXml(array('Subject', 'NameIDPolicy', 'Conditions'), $result, $context);

        // must be last in order signature to include them all
        $this->singleElementsToXml(array('Signature'), $result, $context);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'AuthnRequest', SamlConstants::NS_PROTOCOL);

        parent::deserialize($node, $context);

        $this->attributesFromXml($node, array(
            'ForceAuthn', 'IsPassive', 'ProtocolBinding', 'AssertionConsumerServiceIndex',
            'AssertionConsumerServiceURL', 'AttributeConsumingServiceIndex', 'ProviderName',
        ));

        $this->singleElementsFromXml($node, $context, array(
            'Subject' => array('saml', 'LightSaml\Model\Assertion\Subject'),
            'NameIDPolicy' => array('samlp', 'LightSaml\Model\Protocol\NameIDPolicy'),
            'Conditions' => array('saml', 'LightSaml\Model\Assertion\Conditions'),
        ));
    }
}
