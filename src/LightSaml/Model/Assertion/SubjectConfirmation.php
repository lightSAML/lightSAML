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

class SubjectConfirmation extends AbstractSamlModel
{
    /** @var string */
    protected $method;

    /** @var NameID|null */
    protected $nameId;

    /** @var EncryptedElement|null */
    protected $encryptedId;

    /** @var SubjectConfirmationData|null */
    protected $subjectConfirmationData;

    /**
     * @param string $method
     *
     * @return SubjectConfirmation
     */
    public function setMethod($method)
    {
        $this->method = (string) $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param EncryptedElement|null $encryptedId
     *
     * @return SubjectConfirmation
     */
    public function setEncryptedId(EncryptedElement $encryptedId = null)
    {
        $this->encryptedId = $encryptedId;

        return $this;
    }

    /**
     * @return EncryptedElement|null
     */
    public function getEncryptedId()
    {
        return $this->encryptedId;
    }

    /**
     * @param NameID|null $nameId
     *
     * @return SubjectConfirmation
     */
    public function setNameID(NameID $nameId = null)
    {
        $this->nameId = $nameId;

        return $this;
    }

    /**
     * @return \LightSaml\Model\Assertion\NameID|null
     */
    public function getNameID()
    {
        return $this->nameId;
    }

    /**
     * @param SubjectConfirmationData|null $subjectConfirmationData
     *
     * @return SubjectConfirmation
     */
    public function setSubjectConfirmationData(SubjectConfirmationData $subjectConfirmationData = null)
    {
        $this->subjectConfirmationData = $subjectConfirmationData;

        return $this;
    }

    /**
     * @return SubjectConfirmationData|null
     */
    public function getSubjectConfirmationData()
    {
        return $this->subjectConfirmationData;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('SubjectConfirmation', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->attributesToXml(array('Method'), $result);

        $this->singleElementsToXml(
            array('NameID', 'EncryptedID', 'SubjectConfirmationData'),
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
        $this->checkXmlNodeName($node, 'SubjectConfirmation', SamlConstants::NS_ASSERTION);

        $this->attributesFromXml($node, array('Method'));

        $this->singleElementsFromXml($node, $context, array(
            'NameID' => array('saml', 'LightSaml\Model\Assertion\NameID'),
            'EncryptedID' => array('saml', 'LightSaml\Model\Assertion\EncryptedID'),
            'SubjectConfirmationData' => array('saml', 'LightSaml\Model\Assertion\SubjectConfirmationData'),
        ));
    }
}
