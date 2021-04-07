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

use LightSaml\Model\AbstractSamlModel;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\SamlConstants;

class Subject extends AbstractSamlModel
{
    /** @var NameID */
    protected $nameId;

    /** @var SubjectConfirmation[] */
    protected $subjectConfirmation = [];

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
     * Returns array of <SubjectConfirmation> containing a Method of urn:oasis:names:tc:SAML:2.0:cm:bearer.
     *
     * @return SubjectConfirmation[]
     */
    public function getBearerConfirmations()
    {
        $result = [];
        if ($this->getAllSubjectConfirmations()) {
            foreach ($this->getAllSubjectConfirmations() as $confirmation) {
                if (SamlConstants::CONFIRMATION_METHOD_BEARER == $confirmation->getMethod()) {
                    $result[] = $confirmation;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('Subject', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->singleElementsToXml(['NameID'], $result, $context);
        $this->manyElementsToXml($this->getAllSubjectConfirmations(), $result, $context, null);
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'Subject', SamlConstants::NS_ASSERTION);

        $this->singleElementsFromXml($node, $context, [
            'NameID' => ['saml', 'LightSaml\Model\Assertion\NameID'],
        ]);

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
