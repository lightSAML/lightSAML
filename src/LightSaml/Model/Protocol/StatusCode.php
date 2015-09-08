<?php

namespace LightSaml\Model\Protocol;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\SamlConstants;

class StatusCode extends AbstractSamlModel
{
    /** @var  string */
    protected $value;

    /** @var  StatusCode|null */
    protected $statusCode;

    /**
     * @param string $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = (string) $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param StatusCode|null $statusCode
     *
     * @return StatusCode
     */
    public function setStatusCode(StatusCode $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return StatusCode|null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('samlp:StatusCode', SamlConstants::NS_PROTOCOL, $parent, $context);

        $this->attributesToXml(array('Value'), $result);

        $this->singleElementsToXml(array('StatusCode'), $result, $context);
    }

    /**
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'StatusCode', SamlConstants::NS_PROTOCOL);

        $this->attributesFromXml($node, array('Value'));

        $this->singleElementsFromXml($node, $context, array(
            'StatusCode' => array('samlp', 'LightSaml\Model\Protocol\StatusCode'),
        ));
    }
}
