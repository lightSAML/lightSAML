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

use LightSaml\Helper;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Assertion\NameID;
use LightSaml\SamlConstants;

class LogoutRequest extends AbstractRequest
{
    /** @var string|null */
    protected $reason;

    /** @var int|null */
    protected $notOnOrAfter;

    /** @var NameID */
    protected $nameID;

    /** @var string|null */
    protected $sessionIndex;

    /**
     * @param NameID $nameID
     *
     * @return LogoutRequest
     */
    public function setNameID(NameID $nameID)
    {
        $this->nameID = $nameID;

        return $this;
    }

    /**
     * @return NameID
     */
    public function getNameID()
    {
        return $this->nameID;
    }

    /**
     * @param int|\DateTime|string $notOnOrAfter
     *
     * @return LogoutRequest
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

        return null;
    }

    /**
     * @return \DateTime|null
     */
    public function getNotOnOrAfterDateTime()
    {
        if ($this->notOnOrAfter) {
            return new \DateTime('@'.$this->notOnOrAfter);
        }

        return null;
    }

    /**
     * @param null|string $reason
     *
     * @return LogoutRequest
     */
    public function setReason($reason)
    {
        $this->reason = (string) $reason;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param null|string $sessionIndex
     *
     * @return LogoutRequest
     */
    public function setSessionIndex($sessionIndex)
    {
        $this->sessionIndex = (string) $sessionIndex;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSessionIndex()
    {
        return $this->sessionIndex;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('LogoutRequest', SamlConstants::NS_PROTOCOL, $parent, $context);

        parent::serialize($result, $context);

        $this->attributesToXml(array('Reason', 'NotOnOrAfter'), $result);

        $this->singleElementsToXml(array('NameID', 'SessionIndex'), $result, $context, SamlConstants::NS_PROTOCOL);

        // must be last in order signature to include them all
        $this->singleElementsToXml(array('Signature'), $result, $context);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'LogoutRequest', SamlConstants::NS_PROTOCOL);

        parent::deserialize($node, $context);

        $this->attributesFromXml($node, array('Reason', 'NotOnOrAfter'));

        $this->singleElementsFromXml($node, $context, array(
            'NameID' => array('saml', 'LightSaml\Model\Assertion\NameID'),
            'SessionIndex' => array('samlp', null),
        ));
    }
}
