<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     *
     * @return void
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context);
}
