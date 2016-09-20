<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Metadata;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\AbstractSamlModel;

abstract class Endpoint extends AbstractSamlModel
{
    /** @var string */
    protected $binding;

    /** @var string */
    protected $location;

    /** @var string|null */
    protected $responseLocation;

    /**
     * @param string $location
     * @param string $binding
     */
    public function __construct($location = null, $binding = null)
    {
        $this->location = $location;
        $this->binding = $binding;
    }

    /**
     * @param string $binding
     *
     * @return Endpoint
     */
    public function setBinding($binding)
    {
        $this->binding = (string) $binding;

        return $this;
    }

    /**
     * @return string
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * @param string $location
     *
     * @return Endpoint
     */
    public function setLocation($location)
    {
        $this->location = (string) $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param null|string $responseLocation
     *
     * @return Endpoint
     */
    public function setResponseLocation($responseLocation)
    {
        $this->responseLocation = $responseLocation ? (string) $responseLocation : null;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getResponseLocation()
    {
        return $this->responseLocation;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $this->attributesToXml(array('Binding', 'Location', 'ResponseLocation'), $parent);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->attributesFromXml($node, array('Binding', 'Location', 'ResponseLocation'));
    }
}
