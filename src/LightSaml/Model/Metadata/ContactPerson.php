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

use LightSaml\Model\AbstractSamlModel;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
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
     * @param string|null $company
     *
     * @return ContactPerson
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string|null $emailAddress
     *
     * @return ContactPerson
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param string|null $givenName
     *
     * @return ContactPerson
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param string|null $surName
     *
     * @return ContactPerson
     */
    public function setSurName($surName)
    {
        $this->surName = $surName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSurName()
    {
        return $this->surName;
    }

    /**
     * @param string|null $telephoneNumber
     *
     * @return ContactPerson
     */
    public function setTelephoneNumber($telephoneNumber)
    {
        $this->telephoneNumber = $telephoneNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('ContactPerson', SamlConstants::NS_METADATA, $parent, $context);

        $this->attributesToXml(['contactType'], $result);

        $this->singleElementsToXml(
            ['Company', 'GivenName', 'SurName', 'EmailAddress', 'TelephoneNumber'],
            $result,
            $context,
            SamlConstants::NS_METADATA
        );
    }

    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'ContactPerson', SamlConstants::NS_METADATA);

        $this->attributesFromXml($node, ['contactType']);

        $this->singleElementsFromXml($node, $context, [
            'Company' => ['md', null],
            'GivenName' => ['md', null],
            'SurName' => ['md', null],
            'EmailAddress' => ['md', null],
            'TelephoneNumber' => ['md', null],
        ]);
    }
}
