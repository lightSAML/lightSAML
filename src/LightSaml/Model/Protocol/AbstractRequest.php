<?php

namespace LightSaml\Model\Protocol;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;

abstract class AbstractRequest extends SamlMessage
{
    /** @var string|null */
    protected $consent;

    /**
     * @param null|string $consent
     *
     * @return AbstractRequest
     */
    public function setConsent($consent)
    {
        $this->consent = (string) $consent;

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
        $this->attributesToXml(array('ID', 'Version', 'IssueInstant', 'Consent'), $parent);

        $this->singleElementsToXml(array('Issuer'), $parent, $context);

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
        $this->attributesFromXml($node, array('Consent'));

        parent::deserialize($node, $context);
    }
}
