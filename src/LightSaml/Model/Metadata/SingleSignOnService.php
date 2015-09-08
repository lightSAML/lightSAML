<?php

namespace LightSaml\Model\Metadata;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class SingleSignOnService extends Endpoint
{
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('SingleSignOnService', SamlConstants::NS_METADATA, $parent, $context);

        parent::serialize($result, $context);
    }

    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'SingleSignOnService', SamlConstants::NS_METADATA);

        parent::deserialize($node, $context);
    }
}
