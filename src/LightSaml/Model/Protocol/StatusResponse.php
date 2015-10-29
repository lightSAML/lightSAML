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

    /** @var  string|null */
    protected $consent;

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
     * @param Status $status
     *
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
     * @param null|string $consent
     *
     * @return StatusResponse
     */
    public function setConsent($consent)
    {
        $this->consent = $consent;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getConsent()
    {
        return $this->consent;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $this->attributesToXml(array('ID', 'InResponseTo', 'Version', 'IssueInstant', 'Consent'), $parent);

        $this->singleElementsToXml(array('Issuer', 'Status'), $parent, $context);

        parent::serialize($parent, $context);
    }

    /**
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        $this->attributesFromXml($node, array('InResponseTo', 'Consent'));

        $this->singleElementsFromXml($node, $context, array(
            'Status' => array('samlp', 'LightSaml\Model\Protocol\Status'),
        ));

        parent::deserialize($node, $context);
    }
}
