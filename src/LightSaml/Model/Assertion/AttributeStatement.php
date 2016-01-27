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
use LightSaml\SamlConstants;

class AttributeStatement extends AbstractStatement
{
    /**
     * @var Attribute[]
     */
    protected $attributes = array();

    /**
     * @param Attribute $attribute
     *
     * @return AttributeStatement
     */
    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * @return \LightSaml\Model\Assertion\Attribute[]
     */
    public function getAllAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     *
     * @return Attribute|null
     */
    public function getFirstAttributeByName($name)
    {
        if (is_array($this->getAllAttributes())) {
            foreach ($this->getAllAttributes() as $attribute) {
                if (null == $name || $attribute->getName() == $name) {
                    return $attribute;
                }
            }
        }

        return null;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('AttributeStatement', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->manyElementsToXml($this->getAllAttributes(), $result, $context, null);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'AttributeStatement', SamlConstants::NS_ASSERTION);

        $this->attributes = array();
        $this->manyElementsFromXml(
            $node,
            $context,
            'Attribute',
            'saml',
            'LightSaml\Model\Assertion\Attribute',
            'addAttribute'
        );
    }
}
