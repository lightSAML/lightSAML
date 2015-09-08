<?php

namespace LightSaml\Model\Protocol;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\SamlConstants;

class Status extends AbstractSamlModel
{
    /** @var  StatusCode */
    protected $statusCode;

    /** @var string|null */
    protected $statusMessage;

    /**
     * @param StatusCode|null $statusCode
     * @param string          $message
     */
    public function __construct(StatusCode $statusCode = null, $message = null)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    /**
     * @param StatusCode $statusCode
     *
     * @return Status
     */
    public function setStatusCode(StatusCode $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return StatusCode
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string|null $message
     */
    public function setStatusMessage($message)
    {
        $this->statusMessage = (string) $message;
    }

    /**
     * @return string|null
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        $result = $this->getStatusCode() && $this->getStatusCode()->getValue() == SamlConstants::STATUS_SUCCESS;

        return $result;
    }

    /**
     * @return Status
     */
    public function setSuccess()
    {
        $this->setStatusCode(new StatusCode());
        $this->getStatusCode()->setValue(SamlConstants::STATUS_SUCCESS);

        return $this;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('samlp:Status', SamlConstants::NS_PROTOCOL, $parent, $context);

        $this->singleElementsToXml(array('StatusCode', 'StatusMessage'), $result, $context, SamlConstants::NS_PROTOCOL);
    }

    /**
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'Status', SamlConstants::NS_PROTOCOL);

        $this->singleElementsFromXml($node, $context, array(
            'StatusCode' => array('samlp', 'LightSaml\Model\Protocol\StatusCode'),
            'StatusMessage' => array('samlp', null),
        ));
    }
}
