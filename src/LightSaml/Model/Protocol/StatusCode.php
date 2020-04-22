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

use LightSaml\Model\AbstractSamlModel;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class StatusCode extends AbstractSamlModel
{
    /** @var string */
    protected $value;

    /** @var StatusCode|null */
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
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('samlp:StatusCode', SamlConstants::NS_PROTOCOL, $parent, $context);

        $this->attributesToXml(['Value'], $result);

        $this->singleElementsToXml(['StatusCode'], $result, $context);
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'StatusCode', SamlConstants::NS_PROTOCOL);

        $this->attributesFromXml($node, ['Value']);

        $this->singleElementsFromXml($node, $context, [
            'StatusCode' => ['samlp', 'LightSaml\Model\Protocol\StatusCode'],
        ]);
    }
}
