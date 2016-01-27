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
use LightSaml\Model\AbstractSamlModel;
use LightSaml\SamlConstants;

class NameIDPolicy extends AbstractSamlModel
{
    /**
     * @var string|null
     */
    protected $format;

    /**
     * @var bool|null
     */
    protected $allowCreate;

    /**
     * @var string|null
     */
    protected $spNameQualifier;

    /**
     * @param string $format
     * @param bool   $allowCreate
     */
    public function __construct($format = null, $allowCreate = null)
    {
        $this->allowCreate = $allowCreate;
        $this->format = $format;
    }

    /**
     * @param string|bool|null $allowCreate
     *
     * @return NameIDPolicy
     */
    public function setAllowCreate($allowCreate)
    {
        if ($allowCreate === null) {
            $this->allowCreate = null;
        } elseif (is_string($allowCreate) || is_int($allowCreate)) {
            $this->allowCreate = strcasecmp($allowCreate, 'true') == 0 || $allowCreate === true || $allowCreate == 1;
        } else {
            $this->allowCreate = (bool) $allowCreate;
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAllowCreate()
    {
        return $this->allowCreate;
    }

    /**
     * @return string|null
     */
    public function getAllowCreateString()
    {
        if ($this->allowCreate === null) {
            return null;
        }

        return $this->allowCreate ? 'true' : 'false';
    }

    /**
     * @param string|null $format
     *
     * @return NameIDPolicy
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
     * @param string|null $spNameQualifier
     *
     * @return NameIDPolicy
     */
    public function setSPNameQualifier($spNameQualifier)
    {
        $this->spNameQualifier = $spNameQualifier;

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
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('NameIDPolicy', SamlConstants::NS_PROTOCOL, $parent, $context);

        $this->attributesToXml(array('Format', 'SPNameQualifier', 'AllowCreate'), $result);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'NameIDPolicy', SamlConstants::NS_PROTOCOL);

        $this->attributesFromXml($node, array('Format', 'SPNameQualifier', 'AllowCreate'));
    }
}
