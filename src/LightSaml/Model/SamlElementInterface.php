<?php

namespace LightSaml\Model;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;

interface SamlElementInterface
{
    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context);

    /**
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context);
}
