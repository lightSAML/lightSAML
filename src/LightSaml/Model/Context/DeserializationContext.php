<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Context;

use LightSaml\SamlConstants;
use RobRichards\XMLSecLibs\XMLSecEnc;

class DeserializationContext
{
    /** @var \DOMDocument */
    private $document;

    /** @var \DOMXPath */
    private $xpath;

    /**
     * @param \DOMDocument $document
     */
    public function __construct(\DOMDocument $document = null)
    {
        $this->document = $document ? $document : new \DOMDocument();
    }

    /**
     * @return \DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param \DOMDocument $document
     *
     * @return DeserializationContext
     */
    public function setDocument(\DOMDocument $document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * @return \DOMXPath
     */
    public function getXpath()
    {
        if (null == $this->xpath) {
            $this->xpath = new \DOMXPath($this->document);
            $this->xpath->registerNamespace('saml', SamlConstants::NS_ASSERTION);
            $this->xpath->registerNamespace('samlp', SamlConstants::NS_PROTOCOL);
            $this->xpath->registerNamespace('md', SamlConstants::NS_METADATA);
            $this->xpath->registerNamespace('ds', SamlConstants::NS_XMLDSIG);
            $this->xpath->registerNamespace('xenc', XMLSecEnc::XMLENCNS);
        }

        return $this->xpath;
    }
}
