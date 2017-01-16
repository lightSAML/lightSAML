<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Metadata;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class SpSsoDescriptor extends SSODescriptor
{
    /** @var bool|null */
    protected $authnRequestsSigned;

    /** @var bool|null */
    protected $wantAssertionsSigned;

    /** @var AssertionConsumerService[]|null */
    protected $assertionConsumerServices;

    /**
     * @param AssertionConsumerService $assertionConsumerService
     *
     * @return SpSsoDescriptor
     */
    public function addAssertionConsumerService(AssertionConsumerService $assertionConsumerService)
    {
        if (false == is_array($this->assertionConsumerServices)) {
            $this->assertionConsumerServices = array();
        }
        if (null === $assertionConsumerService->getIndex()) {
            $assertionConsumerService->setIndex(count($this->assertionConsumerServices));
        }
        $this->assertionConsumerServices[] = $assertionConsumerService;

        return $this;
    }

    /**
     * @return AssertionConsumerService[]|null
     */
    public function getAllAssertionConsumerServices()
    {
        return $this->assertionConsumerServices;
    }

    /**
     * @param string $binding
     *
     * @return AssertionConsumerService[]
     */
    public function getAllAssertionConsumerServicesByBinding($binding)
    {
        $result = array();
        foreach ($this->getAllAssertionConsumerServices() as $svc) {
            if ($svc->getBinding() == $binding) {
                $result[] = $svc;
            }
        }

        return $result;
    }

    /**
     * @param string $url
     *
     * @return AssertionConsumerService[]
     */
    public function getAllAssertionConsumerServicesByUrl($url)
    {
        $result = array();
        foreach ($this->getAllAssertionConsumerServices() as $svc) {
            if ($svc->getLocation() == $url) {
                $result[] = $svc;
            }
        }

        return $result;
    }

    /**
     * @param int $index
     *
     * @return AssertionConsumerService|null
     */
    public function getAssertionConsumerServicesByIndex($index)
    {
        foreach ($this->getAllAssertionConsumerServices() as $svc) {
            if ($svc->getIndex() == $index) {
                return $svc;
            }
        }

        return null;
    }

    /**
     * @param string|null $binding
     *
     * @return AssertionConsumerService|null
     */
    public function getFirstAssertionConsumerService($binding = null)
    {
        foreach ($this->getAllAssertionConsumerServices() as $svc) {
            if (null == $binding || $svc->getBinding() == $binding) {
                return $svc;
            }
        }

        return null;
    }

    /**
     * @param bool|null $authnRequestsSigned
     *
     * @return SpSsoDescriptor
     */
    public function setAuthnRequestsSigned($authnRequestsSigned)
    {
        $this->authnRequestsSigned = filter_var($authnRequestsSigned, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE]);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAuthnRequestsSigned()
    {
        return $this->authnRequestsSigned;
    }

    /**
     * @param bool|null $wantAssertionsSigned
     *
     * @return SpSsoDescriptor
     */
    public function setWantAssertionsSigned($wantAssertionsSigned)
    {
        $this->wantAssertionsSigned = filter_var($wantAssertionsSigned, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE]);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWantAssertionsSigned()
    {
        return $this->wantAssertionsSigned;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('SPSSODescriptor', SamlConstants::NS_METADATA, $parent, $context);

        parent::serialize($result, $context);

        $this->attributesToXml(array('AuthnRequestsSigned', 'WantAssertionsSigned'), $result);

        $this->manyElementsToXml($this->getAllAssertionConsumerServices(), $result, $context, null);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'SPSSODescriptor', SamlConstants::NS_METADATA);

        parent::deserialize($node, $context);

        $this->attributesFromXml($node, array('AuthnRequestsSigned', 'WantAssertionsSigned'));

        $this->manyElementsFromXml(
            $node,
            $context,
            'AssertionConsumerService',
            'md',
            'LightSaml\Model\Metadata\AssertionConsumerService',
            'addAssertionConsumerService'
        );
    }
}
