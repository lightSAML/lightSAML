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

class SerializationContext
{
    /** @var \DOMDocument */
    protected $document;

    /**
     * @param \DOMDocument $document
     */
    public function __construct(\DOMDocument $document = null)
    {
        $this->document = $document ? $document : new \DOMDocument();
    }

    /**
     * @param \DOMDocument $document
     */
    public function setDocument(\DOMDocument $document)
    {
        $this->document = $document;
    }

    /**
     * @return \DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }
}
