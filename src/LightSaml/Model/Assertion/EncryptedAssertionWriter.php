<?php

namespace LightSaml\Model\Assertion;

use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class EncryptedAssertionWriter extends EncryptedElementWriter
{
    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return \DOMElement
     */
    protected function createRootElement(\DOMNode $parent, SerializationContext $context)
    {
        return $this->createElement('saml:EncryptedAssertion', SamlConstants::NS_ASSERTION, $parent, $context);
    }
}
