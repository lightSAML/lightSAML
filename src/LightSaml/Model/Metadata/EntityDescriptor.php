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

use LightSaml\Helper;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\XmlDSig\Signature;
use LightSaml\SamlConstants;

class EntityDescriptor extends Metadata
{
    /** @var string */
    protected $entityID;

    /** @var int|null */
    protected $validUntil;

    /** @var string|null */
    protected $cacheDuration;

    /** @var string|null */
    protected $id;

    /** @var Signature|null */
    protected $signature;

    /** @var IdpSsoDescriptor[]|SpSsoDescriptor[] */
    protected $items;

    /** @var Organization[]|null */
    protected $organizations;

    /** @var ContactPerson[]|null */
    protected $contactPersons;

    /**
     * @param string $filename
     *
     * @return EntityDescriptor
     */
    public static function load($filename)
    {
        return self::loadXml(file_get_contents($filename));
    }

    /**
     * @param string $xml
     *
     * @return EntityDescriptor
     */
    public static function loadXml($xml)
    {
        $context = new DeserializationContext();
        $context->getDocument()->loadXML($xml);
        $ed = new self();
        $ed->deserialize($context->getDocument(), $context);

        return $ed;
    }

    /**
     * @param string|null $entityId
     * @param array       $items
     */
    public function __construct($entityId = null, array $items = array())
    {
        $this->entityID = $entityId;
        $this->items = $items;
    }

    /**
     * @param ContactPerson $contactPerson
     *
     * @return EntityDescriptor
     */
    public function addContactPerson(ContactPerson $contactPerson)
    {
        if (false == is_array($this->contactPersons)) {
            $this->contactPersons = array();
        }
        $this->contactPersons[] = $contactPerson;

        return $this;
    }

    /**
     * @return ContactPerson[]|null
     */
    public function getAllContactPersons()
    {
        return $this->contactPersons;
    }

    /**
     * @return ContactPerson|null
     */
    public function getFirstContactPerson()
    {
        if (is_array($this->contactPersons) && isset($this->contactPersons[0])) {
            return $this->contactPersons[0];
        }

        return null;
    }

    /**
     * @param \LightSaml\Model\Metadata\Organization $organization
     *
     * @return EntityDescriptor
     */
    public function addOrganization(Organization $organization)
    {
        if (false == is_array($this->organizations)) {
            $this->organizations = array();
        }
        $this->organizations[] = $organization;

        return $this;
    }

    /**
     * @return Organization[]|null
     */
    public function getAllOrganizations()
    {
        return $this->organizations;
    }

    /**
     * @return \LightSaml\Model\Metadata\Organization|null
     */
    public function getFirstOrganization()
    {
        if (is_array($this->organizations) && isset($this->organizations[0])) {
            return $this->organizations[0];
        }

        return null;
    }

    /**
     * @param string|null $cacheDuration
     *
     * @throws \InvalidArgumentException
     *
     * @return EntityDescriptor
     */
    public function setCacheDuration($cacheDuration)
    {
        Helper::validateDurationString($cacheDuration);

        $this->cacheDuration = $cacheDuration;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCacheDuration()
    {
        return $this->cacheDuration;
    }

    /**
     * @param string $entityID
     *
     * @return EntityDescriptor
     */
    public function setEntityID($entityID)
    {
        $this->entityID = (string) $entityID;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityID()
    {
        return $this->entityID;
    }

    /**
     * @param string|null $id
     *
     * @return EntityDescriptor
     */
    public function setID($id)
    {
        $this->id = $id !== null ? (string) $id : null;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @param \LightSaml\Model\Metadata\IdpSsoDescriptor|\LightSaml\Model\Metadata\SpSsoDescriptor $item
     *
     * @throws \InvalidArgumentException
     *
     * @return EntityDescriptor
     */
    public function addItem($item)
    {
        if (false == $item instanceof IdpSsoDescriptor &&
            false == $item instanceof SpSsoDescriptor
        ) {
            throw new \InvalidArgumentException('EntityDescriptor item must be IdpSsoDescriptor or SpSsoDescriptor');
        }

        if (false == is_array($this->items)) {
            $this->items = array();
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * @return IdpSsoDescriptor[]|SpSsoDescriptor[]|SSODescriptor[]
     */
    public function getAllItems()
    {
        return $this->items;
    }

    /**
     * @return IdpSsoDescriptor[]
     */
    public function getAllIdpSsoDescriptors()
    {
        $result = array();
        foreach ($this->getAllItems() as $item) {
            if ($item instanceof IdpSsoDescriptor) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return SpSsoDescriptor[]
     */
    public function getAllSpSsoDescriptors()
    {
        $result = array();
        foreach ($this->getAllItems() as $item) {
            if ($item instanceof SpSsoDescriptor) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return IdpSsoDescriptor|null
     */
    public function getFirstIdpSsoDescriptor()
    {
        foreach ($this->getAllItems() as $item) {
            if ($item instanceof IdpSsoDescriptor) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return SpSsoDescriptor|null
     */
    public function getFirstSpSsoDescriptor()
    {
        foreach ($this->getAllItems() as $item) {
            if ($item instanceof SpSsoDescriptor) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param Signature|null $signature
     *
     * @return EntityDescriptor
     */
    public function setSignature(Signature $signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return Signature|null
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param int $validUntil
     *
     * @return EntityDescriptor
     */
    public function setValidUntil($validUntil)
    {
        $this->validUntil = Helper::getTimestampFromValue($validUntil);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getValidUntilTimestamp()
    {
        return $this->validUntil;
    }

    /**
     * @return string|null
     */
    public function getValidUntilString()
    {
        if ($this->validUntil) {
            return Helper::time2string($this->validUntil);
        }

        return null;
    }

    /**
     * @return \DateTime|null
     */
    public function getValidUntilDateTime()
    {
        if ($this->validUntil) {
            return new \DateTime('@'.$this->validUntil);
        }

        return null;
    }

    /**
     * @return array|KeyDescriptor[]
     */
    public function getAllIdpKeyDescriptors()
    {
        $result = array();
        foreach ($this->getAllIdpSsoDescriptors() as $idp) {
            foreach ($idp->getAllKeyDescriptors() as $key) {
                $result[] = $key;
            }
        }

        return $result;
    }

    /**
     * @return array|KeyDescriptor[]
     */
    public function getAllSpKeyDescriptors()
    {
        $result = array();
        foreach ($this->getAllSpSsoDescriptors() as $sp) {
            foreach ($sp->getAllKeyDescriptors() as $key) {
                $result[] = $key;
            }
        }

        return $result;
    }

    /**
     * @return EndpointReference[]
     */
    public function getAllEndpoints()
    {
        $result = array();
        foreach ($this->getAllIdpSsoDescriptors() as $idpSsoDescriptor) {
            foreach ($idpSsoDescriptor->getAllSingleSignOnServices() as $sso) {
                $result[] = new EndpointReference($this, $idpSsoDescriptor, $sso);
            }
            foreach ($idpSsoDescriptor->getAllSingleLogoutServices() as $slo) {
                $result[] = new EndpointReference($this, $idpSsoDescriptor, $slo);
            }
        }
        foreach ($this->getAllSpSsoDescriptors() as $spSsoDescriptor) {
            foreach ($spSsoDescriptor->getAllAssertionConsumerServices() as $acs) {
                $result[] = new EndpointReference($this, $spSsoDescriptor, $acs);
            }
            foreach ($spSsoDescriptor->getAllSingleLogoutServices() as $slo) {
                $result[] = new EndpointReference($this, $spSsoDescriptor, $slo);
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
        $result = $this->createElement('EntityDescriptor', SamlConstants::NS_METADATA, $parent, $context);

        $this->attributesToXml(array('entityID', 'validUntil', 'cacheDuration', 'ID'), $result);

        $this->manyElementsToXml($this->getAllItems(), $result, $context, null);
        if ($this->organizations) {
            $this->manyElementsToXml($this->organizations, $result, $context, null);
        }
        if ($this->contactPersons) {
            $this->manyElementsToXml($this->contactPersons, $result, $context, null);
        }

        $this->singleElementsToXml(array('Signature'), $result, $context);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'EntityDescriptor', SamlConstants::NS_METADATA);

        $this->attributesFromXml($node, array('entityID', 'validUntil', 'cacheDuration', 'ID'));

        $this->items = array();

        $this->manyElementsFromXml(
            $node,
            $context,
            'IDPSSODescriptor',
            'md',
            'LightSaml\Model\Metadata\IdpSsoDescriptor',
            'addItem'
        );

        $this->manyElementsFromXml(
            $node,
            $context,
            'SPSSODescriptor',
            'md',
            'LightSaml\Model\Metadata\SpSsoDescriptor',
            'addItem'
        );

        $this->manyElementsFromXml(
            $node,
            $context,
            'Organization',
            'md',
            'LightSaml\Model\Metadata\Organization',
            'addOrganization'
        );

        $this->manyElementsFromXml(
            $node,
            $context,
            'ContactPerson',
            'md',
            'LightSaml\Model\Metadata\ContactPerson',
            'addContactPerson'
        );

        $this->singleElementsFromXml($node, $context, array(
            'Signature' => array('ds', 'LightSaml\Model\XmlDSig\SignatureXmlReader'),
        ));
    }
}
