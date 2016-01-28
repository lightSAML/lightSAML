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

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\SamlConstants;

class Attribute extends AbstractSamlModel
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $nameFormat;

    /** @var string */
    protected $friendlyName;

    /** @var string[] */
    protected $attributeValue;

    /**
     * @param string|null     $name
     * @param string|string[] $value
     */
    public function __construct($name = null, $value = null)
    {
        $this->name = $name;
        if ($value) {
            $this->attributeValue = is_array($value) ? $value : array($value);
        }
    }

    /**
     * @param string $attributeValue
     *
     * @return Attribute
     */
    public function addAttributeValue($attributeValue)
    {
        if (false == is_array($this->attributeValue)) {
            $this->attributeValue = array();
        }
        $this->attributeValue[] = $attributeValue;

        return $this;
    }

    /**
     * @param string[]|string $attributeValue
     *
     * @return Attribute
     */
    public function setAttributeValue($attributeValue)
    {
        if (false == is_array($attributeValue)) {
            $attributeValue = array($attributeValue);
        }
        $this->attributeValue = $attributeValue;

        return $this;
    }

    /**
     * @return \string[]
     */
    public function getAllAttributeValues()
    {
        return $this->attributeValue;
    }

    /**
     * @return string|null
     */
    public function getFirstAttributeValue()
    {
        $arr = $this->attributeValue;

        return array_shift($arr);
    }

    /**
     * @param string $friendlyName
     *
     * @return Attribute
     */
    public function setFriendlyName($friendlyName)
    {
        $this->friendlyName = $friendlyName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }

    /**
     * @param string $name
     *
     * @return Attribute
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $nameFormat
     *
     * @return Attribute
     */
    public function setNameFormat($nameFormat)
    {
        $this->nameFormat = $nameFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameFormat()
    {
        return $this->nameFormat;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('Attribute', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->attributesToXml(array('Name', 'NameFormat', 'FriendlyName'), $result);

        $this->manyElementsToXml($this->getAllAttributeValues(), $result, $context, 'AttributeValue', SamlConstants::NS_ASSERTION);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'Attribute', SamlConstants::NS_ASSERTION);

        $this->attributesFromXml($node, array('Name', 'NameFormat', 'FriendlyName'));

        $this->attributeValue = array();
        $this->manyElementsFromXml($node, $context, 'AttributeValue', 'saml', null, 'addAttributeValue');
    }
}
