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
use LightSaml\SamlConstants;

class AuthnStatement extends AbstractStatement
{
    /**
     * @var int|null
     */
    protected $authnInstant;

    /**
     * @var int|null
     */
    protected $sessionNotOnOrAfter;

    /**
     * @var string|null
     */
    protected $sessionIndex;

    /**
     * @var AuthnContext
     */
    protected $authnContext;

    /**
     * @var SubjectLocality
     */
    protected $subjectLocality;

    /**
     * @param AuthnContext $authnContext
     *
     * @return AuthnStatement
     */
    public function setAuthnContext(AuthnContext $authnContext)
    {
        $this->authnContext = $authnContext;

        return $this;
    }

    /**
     * @return \LightSaml\Model\Assertion\AuthnContext
     */
    public function getAuthnContext()
    {
        return $this->authnContext;
    }

    /**
     * @param int|string|\DateTime $authnInstant
     *
     * @return AuthnStatement
     */
    public function setAuthnInstant($authnInstant)
    {
        $this->authnInstant = Helper::getTimestampFromValue($authnInstant);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAuthnInstantTimestamp()
    {
        return $this->authnInstant;
    }

    /**
     * @return string|null
     */
    public function getAuthnInstantString()
    {
        if ($this->authnInstant) {
            return Helper::time2string($this->authnInstant);
        }

        return null;
    }

    /**
     * @return \DateTime|null
     */
    public function getAuthnInstantDateTime()
    {
        if ($this->authnInstant) {
            return new \DateTime('@'.$this->authnInstant);
        }

        return null;
    }

    /**
     * @param null|string $sessionIndex
     *
     * @return AuthnStatement
     */
    public function setSessionIndex($sessionIndex)
    {
        $this->sessionIndex = $sessionIndex;

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
     * @param int|string|\DateTime $sessionNotOnOrAfter
     *
     * @return AuthnStatement
     */
    public function setSessionNotOnOrAfter($sessionNotOnOrAfter)
    {
        $this->sessionNotOnOrAfter = Helper::getTimestampFromValue($sessionNotOnOrAfter);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSessionNotOnOrAfterTimestamp()
    {
        return $this->sessionNotOnOrAfter;
    }

    /**
     * @return string|null
     */
    public function getSessionNotOnOrAfterString()
    {
        if ($this->sessionNotOnOrAfter) {
            return Helper::time2string($this->sessionNotOnOrAfter);
        }

        return null;
    }

    /**
     * @return \DateTime|null
     */
    public function getSessionNotOnOrAfterDateTime()
    {
        if ($this->sessionNotOnOrAfter) {
            return new \DateTime('@'.$this->sessionNotOnOrAfter);
        }

        return null;
    }

    /**
     * @param SubjectLocality $subjectLocality
     *
     * @return AuthnStatement
     */
    public function setSubjectLocality($subjectLocality)
    {
        $this->subjectLocality = $subjectLocality;

        return $this;
    }

    /**
     * @return \LightSaml\Model\Assertion\SubjectLocality
     */
    public function getSubjectLocality()
    {
        return $this->subjectLocality;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('AuthnStatement', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->attributesToXml(
            array('AuthnInstant', 'SessionNotOnOrAfter', 'SessionIndex'),
            $result
        );

        $this->singleElementsToXml(
            array('SubjectLocality', 'AuthnContext'),
            $result,
            $context
        );
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'AuthnStatement', SamlConstants::NS_ASSERTION);

        $this->attributesFromXml($node, array('AuthnInstant', 'SessionNotOnOrAfter', 'SessionIndex'));

        $this->singleElementsFromXml($node, $context, array(
            'SubjectLocality' => array('saml', 'LightSaml\Model\Assertion\SubjectLocality'),
            'AuthnContext' => array('saml', 'LightSaml\Model\Assertion\AuthnContext'),
        ));
    }
}
