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
use LightSaml\SamlConstants;

class LogoutResponse extends StatusResponse
{
    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('samlp:LogoutResponse', SamlConstants::NS_PROTOCOL, $parent, $context);

        parent::serialize($result, $context);

        // must be done here at the end and not in a base class where declared in order to include signing of the elements added here
        $this->singleElementsToXml(array('Signature'), $result, $context);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'LogoutResponse', SamlConstants::NS_PROTOCOL);

        parent::deserialize($node, $context);
    }
}
