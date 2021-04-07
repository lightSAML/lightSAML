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

use LightSaml\Model\AbstractSamlModel;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
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
        if (null === $allowCreate) {
            $this->allowCreate = null;
        } elseif (is_string($allowCreate) || is_int($allowCreate)) {
            $this->allowCreate = 0 == strcasecmp($allowCreate, 'true') || true === $allowCreate || 1 == $allowCreate;
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
        if (null === $this->allowCreate) {
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
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('NameIDPolicy', SamlConstants::NS_PROTOCOL, $parent, $context);

        $this->attributesToXml(['Format', 'SPNameQualifier', 'AllowCreate'], $result);
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'NameIDPolicy', SamlConstants::NS_PROTOCOL);

        $this->attributesFromXml($node, ['Format', 'SPNameQualifier', 'AllowCreate']);
    }
}
