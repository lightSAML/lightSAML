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

abstract class StatusResponse extends SamlMessage
{
    /** @var string */
    protected $inResponseTo;

    /** @var Status */
    protected $status;

    /**
     * @param string $inResponseTo
     *
     * @return StatusResponse
     */
    public function setInResponseTo($inResponseTo)
    {
        $this->inResponseTo = $inResponseTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getInResponseTo()
    {
        return $this->inResponseTo;
    }

    /**
     * @return StatusResponse
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        parent::serialize($parent, $context);

        $this->attributesToXml(['InResponseTo'], $parent);

        $this->singleElementsToXml(['Status'], $parent, $context);
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->attributesFromXml($node, ['InResponseTo']);

        $this->singleElementsFromXml($node, $context, [
            'Status' => ['samlp', 'LightSaml\Model\Protocol\Status'],
        ]);

        parent::deserialize($node, $context);
    }
}
