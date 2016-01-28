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

class AssertionConsumerService extends IndexedEndpoint
{
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('AssertionConsumerService', SamlConstants::NS_METADATA, $parent, $context);
        parent::serialize($result, $context);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'AssertionConsumerService', SamlConstants::NS_METADATA);
        parent::deserialize($node, $context);
    }
}
