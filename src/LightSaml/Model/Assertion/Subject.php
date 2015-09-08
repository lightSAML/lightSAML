<?php

namespace LightSaml\Model\Assertion;

use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\AbstractSamlModel;
use LightSaml\SamlConstants;

class Subject extends AbstractSamlModel
{
    /** @var NameID */
    protected $nameId;

    /** @var SubjectConfirmation[] */
    protected $subjectConfirmation = array();

    /**
     * @param NameID $nameId
     *
     * @return Subject
     */
    public function setNameID(NameID $nameId = null)
    {
        $this->nameId = $nameId;

        return $this;
    }

    /**
     * @return \LightSaml\Model\Assertion\NameID
     */
    public function getNameID()
    {
        return $this->nameId;
    }

    /**
     * @param SubjectConfirmation $subjectConfirmation
     *
     * @return Subject
     */
    public function addSubjectConfirmation(SubjectConfirmation $subjectConfirmation)
    {
        $this->subjectConfirmation[] = $subjectConfirmation;

        return $this;
    }

    /**
     * @return SubjectConfirmation[]
     */
    public function getAllSubjectConfirmations()
    {
        return $this->subjectConfirmation;
    }

    /**
     * @return SubjectConfirmation|null
     */
    public function getFirstSubjectConfirmation()
    {
        if (is_array($this->subjectConfirmation) && isset($this->subjectConfirmation[0])) {
            return $this->subjectConfirmation[0];
        }

        return;
    }

    /**
     * Returns array of <SubjectConfirmation> containing a Method of urn:oasis:names:tc:SAML:2.0:cm:bearer
     *
     * @return SubjectConfirmation[]
     */
    public function getBearerConfirmations()
    {
        $result = array();
        if ($this->getAllSubjectConfirmations()) {
            foreach ($this->getAllSubjectConfirmations() as $confirmation) {
                if ($confirmation->getMethod() == SamlConstants::CONFIRMATION_METHOD_BEARER) {
                    $result[] = $confirmation;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('Subject', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->singleElementsToXml(array('NameID'), $result, $context);
        $this->manyElementsToXml($this->getAllSubjectConfirmations(), $result, $context, null);
    }

    /**
     * @param \DOMElement            $node
     * @param DeserializationContext $context
     *
     * @return void
     */
    public function deserialize(\DOMElement $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'Subject', SamlConstants::NS_ASSERTION);

        $this->singleElementsFromXml($node, $context, array(
            'NameID' => array('saml', 'LightSaml\Model\Assertion\NameID'),
        ));

        $this->manyElementsFromXml(
            $node,
            $context,
            'SubjectConfirmation',
            'saml',
            'LightSaml\Model\Assertion\SubjectConfirmation',
            'addSubjectConfirmation'
        );
    }
}
