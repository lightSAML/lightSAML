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
use LightSaml\SamlConstants;

class ContactPerson extends AbstractSamlModel
{
    const TYPE_TECHNICAL = 'technical';
    const TYPE_SUPPORT = 'support';
    const TYPE_ADMINISTRATIVE = 'administrative';
    const TYPE_BILLING = 'billing';
    const TYPE_OTHER = 'other';

    /** @var string */
    protected $contactType;

    /** @var string|null */
    protected $company;

    /** @var string|null */
    protected $givenName;

    /** @var string|null */
    protected $surName;

    /** @var string|null */
    protected $emailAddress;

    /** @var string|null */
    protected $telephoneNumber;

    /**
     * @param string $contactType
     *
     * @return ContactPerson
     */
    public function setContactType($contactType)
    {
        $this->contactType = (string) $contactType;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactType()
    {
        return $this->contactType;
    }

    /**
     * @param null|string $company
     *
     * @return ContactPerson
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param null|string $emailAddress
     *
     * @return ContactPerson
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param null|string $givenName
     *
     * @return ContactPerson
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param null|string $surName
     *
     * @return ContactPerson
     */
    public function setSurName($surName)
    {
        $this->surName = $surName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSurName()
    {
        return $this->surName;
    }

    /**
     * @param null|string $telephoneNumber
     *
     * @return ContactPerson
     */
    public function setTelephoneNumber($telephoneNumber)
    {
        $this->telephoneNumber = $telephoneNumber;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('ContactPerson', SamlConstants::NS_METADATA, $parent, $context);

        $this->attributesToXml(array('contactType'), $result);

        $this->singleElementsToXml(
            array('Company', 'GivenName', 'SurName', 'EmailAddress', 'TelephoneNumber'),
            $result,
            $context,
            SamlConstants::NS_METADATA
        );
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'ContactPerson', SamlConstants::NS_METADATA);

        $this->attributesFromXml($node, array('contactType'));

        $this->singleElementsFromXml($node, $context, array(
            'Company' => array('md', null),
            'GivenName' => array('md', null),
            'SurName' => array('md', null),
            'EmailAddress' => array('md', null),
            'TelephoneNumber' => array('md', null),
        ));
    }
}
