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
use LightSaml\Model\XmlDSig\Signature;
use LightSaml\SamlConstants;

class Assertion extends AbstractSamlModel
{
    //region Attributes

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $version = SamlConstants::VERSION_20;

    /**
     * @var int
     */
    protected $issueInstant;

    //endregion

    //region Elements

    /**
     * @var Issuer
     */
    protected $issuer;

    /**
     * @var Signature|null
     */
    protected $signature;

    /**
     * @var Subject|null
     */
    protected $subject;

    /**
     * @var Conditions|null
     */
    protected $conditions;

    /**
     * @var array|AbstractStatement[]|AuthnStatement[]|AttributeStatement[]
     */
    protected $items = array();

    //endregion

    /**
     * Core 3.3.4 Processing rules.
     *
     * @param string      $nameId
     * @param string|null $format
     *
     * @return bool
     */
    public function equals($nameId, $format)
    {
        if (false == $this->getSubject()) {
            return false;
        }

        if (false == $this->getSubject()->getNameID()) {
            return false;
        }

        if ($this->getSubject()->getNameID()->getValue() != $nameId) {
            return false;
        }

        if ($this->getSubject()->getNameID()->getFormat() != $format) {
            return false;
        }

        return true;
    }

    /**
     * @param string $sessionIndex
     *
     * @return bool
     */
    public function hasSessionIndex($sessionIndex)
    {
        if (null == $this->getAllAuthnStatements()) {
            return false;
        }

        foreach ($this->getAllAuthnStatements() as $authnStatement) {
            if ($authnStatement->getSessionIndex() == $sessionIndex) {
                return true;
            }
        }

        return false;
    }

    public function hasAnySessionIndex()
    {
        if (false == $this->getAllAuthnStatements()) {
            return false;
        }

        foreach ($this->getAllAuthnStatements() as $authnStatement) {
            if ($authnStatement->getSessionIndex()) {
                return true;
            }
        }

        return false;
    }

    //region Getters & Setters

    /**
     * @param Conditions|null $conditions
     *
     * @return Assertion
     */
    public function setConditions(Conditions $conditions = null)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * @return \LightSaml\Model\Assertion\Conditions|null
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param string $id
     *
     * @return Assertion
     */
    public function setId($id)
    {
        $this->id = (string) $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|int|\DateTime $issueInstant
     *
     * @throws \InvalidArgumentException
     *
     * @return Assertion
     */
    public function setIssueInstant($issueInstant)
    {
        $this->issueInstant = Helper::getTimestampFromValue($issueInstant);

        return $this;
    }

    /**
     * @return int
     */
    public function getIssueInstantTimestamp()
    {
        return $this->issueInstant;
    }

    /**
     * @return string
     */
    public function getIssueInstantString()
    {
        if ($this->issueInstant) {
            return Helper::time2string($this->issueInstant);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getIssueInstantDateTime()
    {
        if ($this->issueInstant) {
            return new \DateTime('@'.$this->issueInstant);
        }

        return null;
    }

    /**
     * @param Issuer $issuer
     *
     * @return Assertion
     */
    public function setIssuer(Issuer $issuer = null)
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * @return \LightSaml\Model\Assertion\Issuer
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @param Signature $signature
     *
     * @return Assertion
     */
    public function setSignature(Signature $signature = null)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return \LightSaml\Model\XmlDSig\Signature|null
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param Subject $subject
     *
     * @return Assertion
     */
    public function setSubject(Subject $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return \LightSaml\Model\Assertion\Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $version
     *
     * @return Assertion
     */
    public function setVersion($version)
    {
        $this->version = (string) $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param AbstractStatement $statement
     *
     * @return Assertion
     */
    public function addItem(AbstractStatement $statement)
    {
        $this->items[] = $statement;

        return $this;
    }

    /**
     * @return AbstractStatement[]|AttributeStatement[]|AuthnStatement[]|array
     */
    public function getAllItems()
    {
        return $this->items;
    }

    /**
     * @return \LightSaml\Model\Assertion\AuthnStatement[]
     */
    public function getAllAuthnStatements()
    {
        $result = array();
        foreach ($this->items as $item) {
            if ($item instanceof AuthnStatement) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return \LightSaml\Model\Assertion\AttributeStatement[]
     */
    public function getAllAttributeStatements()
    {
        $result = array();
        foreach ($this->items as $item) {
            if ($item instanceof AttributeStatement) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return \LightSaml\Model\Assertion\AttributeStatement|null
     */
    public function getFirstAttributeStatement()
    {
        foreach ($this->items as $item) {
            if ($item instanceof AttributeStatement) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return \LightSaml\Model\Assertion\AuthnStatement|null
     */
    public function getFirstAuthnStatement()
    {
        foreach ($this->items as $item) {
            if ($item instanceof AuthnStatement) {
                return $item;
            }
        }

        return null;
    }

    //endregion

    /**
     * @return bool
     */
    public function hasBearerSubject()
    {
        if ($this->getAllAuthnStatements() && $this->getSubject()) {
            if ($this->getSubject()->getBearerConfirmations()) {
                return true;
            }
        }

        return false;
    }

    protected function prepareForXml()
    {
        if (false == $this->getId()) {
            $this->setId(Helper::generateID());
        }
        if (false == $this->getIssueInstantTimestamp()) {
            $this->setIssueInstant(time());
        }
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $this->prepareForXml();

        $result = $this->createElement('Assertion', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->attributesToXml(array('ID', 'Version', 'IssueInstant'), $result);

        $this->singleElementsToXml(
            array('Issuer', 'Subject', 'Conditions'),
            $result,
            $context
        );

        foreach ($this->items as $item) {
            $item->serialize($result, $context);
        }

        // must be added at the end
        $this->singleElementsToXml(array('Signature'), $result, $context);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'Assertion', SamlConstants::NS_ASSERTION);

        $this->attributesFromXml($node, array('ID', 'Version', 'IssueInstant'));

        $this->singleElementsFromXml($node, $context, array(
            'Issuer' => array('saml', 'LightSaml\Model\Assertion\Issuer'),
            'Subject' => array('saml', 'LightSaml\Model\Assertion\Subject'),
            'Conditions' => array('saml', 'LightSaml\Model\Assertion\Conditions'),
        ));

        $this->manyElementsFromXml(
            $node,
            $context,
            'AuthnStatement',
            'saml',
            'LightSaml\Model\Assertion\AuthnStatement',
            'addItem'
        );

        $this->manyElementsFromXml(
            $node,
            $context,
            'AttributeStatement',
            'saml',
            'LightSaml\Model\Assertion\AttributeStatement',
            'addItem'
        );

        $this->singleElementsFromXml($node, $context, array(
            'Signature' => array('ds', 'LightSaml\Model\XmlDSig\SignatureXmlReader'),
        ));
    }
}
