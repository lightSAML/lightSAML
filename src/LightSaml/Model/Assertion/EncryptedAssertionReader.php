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
use RobRichards\XMLSecLibs\XMLSecurityKey;

class EncryptedAssertionReader extends EncryptedElementReader
{
    /**
     * @param XMLSecurityKey[]       $inputKeys
     * @param DeserializationContext $deserializationContext
     *
     * @return Assertion
     */
    public function decryptMultiAssertion(array $inputKeys, DeserializationContext $deserializationContext)
    {
        $dom = $this->decryptMulti($inputKeys);

        return $this->getAssertionFromDom($dom, $deserializationContext);
    }

    /**
     * @param XMLSecurityKey         $credential
     * @param DeserializationContext $deserializationContext
     *
     * @return Assertion
     */
    public function decryptAssertion($credential, DeserializationContext $deserializationContext)
    {
        $dom = $this->decrypt($credential);

        return $this->getAssertionFromDom($dom, $deserializationContext);
    }

    /**
     * @param \DOMElement            $dom
     * @param DeserializationContext $deserializationContext
     *
     * @return Assertion
     */
    protected function getAssertionFromDom(\DOMElement $dom, DeserializationContext $deserializationContext)
    {
        $deserializationContext->setDocument($dom->ownerDocument);

        $assertion = new Assertion();
        $assertion->deserialize($dom, $deserializationContext);

        return $assertion;
    }
}
