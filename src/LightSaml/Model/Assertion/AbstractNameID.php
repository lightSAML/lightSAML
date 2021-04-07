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

use LightSaml\Error\LightSamlModelException;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

abstract class AbstractNameID extends AbstractSamlModel
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @var string|null
     */
    protected $format;

    /**
     * @var string|null
     */
    protected $nameQualifier;

    /**
     * @var string|null
     */
    protected $spNameQualifier;

    /**
     * @var string|null
     */
    protected $spProvidedId;

    /**
     * @param string $value
     * @param string $format
     */
    public function __construct($value = null, $format = null)
    {
        $this->value = $value;
        $this->format = $format;
    }

    /**
     * @param string|null $format
     *
     * @return AbstractNameID
     */
    public function setFormat($format)
    {
        $this->format = (string) $format;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string|null $nameQualifier
     *
     * @return AbstractNameID
     */
    public function setNameQualifier($nameQualifier)
    {
        $this->nameQualifier = (string) $nameQualifier;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameQualifier()
    {
        return $this->nameQualifier;
    }

    /**
     * @param string|null $spNameQualifier
     *
     * @return AbstractNameID
     */
    public function setSPNameQualifier($spNameQualifier)
    {
        $this->spNameQualifier = (string) $spNameQualifier;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSPNameQualifier()
    {
        return $this->spNameQualifier;
    }

    /**
     * @param string|null $spProvidedId
     *
     * @return AbstractNameID
     */
    public function setSPProvidedID($spProvidedId)
    {
        $this->spProvidedId = (string) $spProvidedId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSPProvidedID()
    {
        return $this->spProvidedId;
    }

    /**
     * @param string $value
     *
     * @return AbstractNameID
     */
    public function setValue($value)
    {
        $this->value = (string) $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    protected function prepareForXml()
    {
        if (false == $this->getValue()) {
            throw new LightSamlModelException('NameID value not set');
        }
    }

    /**
     * @return \DOMElement
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $this->prepareForXml();
        if (SamlConstants::NS_ASSERTION == $parent->namespaceURI) {
            $result = $this->createElement($this->getElementName(), SamlConstants::NS_ASSERTION, $parent, $context);
        } else {
            $result = $this->createElement('saml:'.$this->getElementName(), SamlConstants::NS_ASSERTION, $parent, $context);
        }

        /* @var \DOMElement $parent */
        $this->attributesToXml(['Format', 'NameQualifier', 'SPNameQualifier', 'SPProvidedID'], $result);
        $result->nodeValue = $this->getValue();
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, $this->getElementName(), SamlConstants::NS_ASSERTION);

        $this->attributesFromXml($node, ['NameQualifier', 'Format', 'SPNameQualifier', 'SPProvidedID']);
        $this->setValue($node->textContent);
    }

    /**
     * @return string
     */
    abstract protected function getElementName();
}
