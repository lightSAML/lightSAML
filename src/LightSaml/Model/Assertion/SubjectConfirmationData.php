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

use LightSaml\Helper;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\SamlConstants;

class SubjectConfirmationData extends AbstractSamlModel
{
    /** @var int|null */
    protected $notBefore;

    /** @var int|null */
    protected $notOnOrAfter;

    /** @var string|null */
    protected $address;

    /** @var string|null */
    protected $inResponseTo;

    /** @var string|null */
    protected $recipient;

    /**
     * @param null|string $address
     *
     * @return SubjectConfirmationData
     */
    public function setAddress($address)
    {
        $this->address = (string) $address;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param null|string $inResponseTo
     *
     * @return SubjectConfirmationData
     */
    public function setInResponseTo($inResponseTo)
    {
        $this->inResponseTo = (string) $inResponseTo;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getInResponseTo()
    {
        return $this->inResponseTo;
    }

    /**
     * @param int|string|\DateTime $notBefore
     *
     * @return SubjectConfirmationData
     */
    public function setNotBefore($notBefore)
    {
        $this->notBefore = Helper::getTimestampFromValue($notBefore);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNotBeforeTimestamp()
    {
        return $this->notBefore;
    }

    /**
     * @return string|null
     */
    public function getNotBeforeString()
    {
        if ($this->notBefore) {
            return Helper::time2string($this->notBefore);
        }

        return;
    }

    /**
     * @return \DateTime|null
     */
    public function getNotBeforeDateTime()
    {
        if ($this->notBefore) {
            return new \DateTime('@'.$this->notBefore);
        }

        return;
    }

    /**
     * @param int|string|\DateTime $notOnOrAfter
     *
     * @return SubjectConfirmationData
     */
    public function setNotOnOrAfter($notOnOrAfter)
    {
        $this->notOnOrAfter = Helper::getTimestampFromValue($notOnOrAfter);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNotOnOrAfterTimestamp()
    {
        return $this->notOnOrAfter;
    }

    /**
     * @return string|null
     */
    public function getNotOnOrAfterString()
    {
        if ($this->notOnOrAfter) {
            return Helper::time2string($this->notOnOrAfter);
        }

        return;
    }

    /**
     * @return \DateTime|null
     */
    public function getNotOnOrAfterDateTime()
    {
        if ($this->notOnOrAfter) {
            return new \DateTime('@'.$this->notOnOrAfter);
        }

        return;
    }

    /**
     * @param null|string $recipient
     *
     * @return SubjectConfirmationData
     */
    public function setRecipient($recipient)
    {
        $this->recipient = (string) $recipient;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('SubjectConfirmationData', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->attributesToXml(
            array('InResponseTo', 'NotBefore', 'NotOnOrAfter', 'Address', 'Recipient'),
            $result
        );
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'SubjectConfirmationData', SamlConstants::NS_ASSERTION);

        $this->attributesFromXml($node, array(
            'InResponseTo', 'NotBefore', 'NotOnOrAfter', 'Address', 'Recipient',
        ));
    }
}
