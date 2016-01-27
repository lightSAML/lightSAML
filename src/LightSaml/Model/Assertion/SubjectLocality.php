<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Model\Assertion;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\SamlConstants;

class SubjectLocality extends AbstractSamlModel
{
    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $dnsName;

    /**
     * @param string $address
     *
     * @return SubjectLocality
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $dnsName
     *
     * @return SubjectLocality
     */
    public function setDNSName($dnsName)
    {
        $this->dnsName = $dnsName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDNSName()
    {
        return $this->dnsName;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('SubjectLocality', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->attributesToXml(array('Address', 'DNSName'), $result);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'SubjectLocality', SamlConstants::NS_ASSERTION);

        $this->attributesFromXml($node, array('Address', 'DNSName'));
    }
}
