<?php

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
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'AssertionConsumerService', SamlConstants::NS_METADATA);
        parent::deserialize($node, $context);
    }
}
