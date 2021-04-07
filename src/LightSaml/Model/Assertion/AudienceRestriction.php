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

class AudienceRestriction extends AbstractCondition
{
    /**
     * @var string[]
     */
    protected $audience = [];

    /**
     * @param string|string[] $audience
     */
    public function __construct($audience = [])
    {
        if (false == is_array($audience)) {
            $audience = [$audience];
        }
        $this->audience = $audience;
    }

    /**
     * @param string $audience
     *
     * @return AudienceRestriction
     */
    public function addAudience($audience)
    {
        $this->audience[] = $audience;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAllAudience()
    {
        return $this->audience;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function hasAudience($value)
    {
        if (is_array($this->audience)) {
            foreach ($this->audience as $a) {
                if ($a == $value) {
                    return true;
                }
            }
        }

        return false;
    }

    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('AudienceRestriction', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->manyElementsToXml($this->getAllAudience(), $result, $context, 'Audience', SamlConstants::NS_ASSERTION);
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'AudienceRestriction', SamlConstants::NS_ASSERTION);

        $this->audience = [];
        $this->manyElementsFromXml($node, $context, 'Audience', 'saml', null, 'addAudience');
    }
}
