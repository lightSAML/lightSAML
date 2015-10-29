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
