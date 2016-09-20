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

class EntitiesDescriptor extends Metadata
{
    /** @var int */
    protected $validUntil;

    /** @var string */
    protected $cacheDuration;

    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var Signature */
    protected $signature;

    /** @var EntitiesDescriptor[]|EntityDescriptor[] */
    protected $items = array();

    /**
     * @param string $filename
     *
     * @return EntitiesDescriptor
     */
    public static function load($filename)
    {
        return self::loadXml(file_get_contents($filename));
    }

    /**
     * @param string $xml
     *
     * @return EntitiesDescriptor
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
     * @param string $cacheDuration
     *
     * @return EntitiesDescriptor
     *
     * @throws \InvalidArgumentException
     */
    public function setCacheDuration($cacheDuration)
    {
        Helper::validateDurationString($cacheDuration);

        $this->cacheDuration = $cacheDuration;

        return $this;
    }

    /**
     * @return string
     */
    public function getCacheDuration()
    {
        return $this->cacheDuration;
    }

    /**
     * @param string $id
     *
     * @return EntitiesDescriptor
     */
    public function setID($id)
    {
        $this->id = (string) $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return EntitiesDescriptor
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \LightSaml\Model\XmlDSig\Signature $signature
     *
     * @return EntitiesDescriptor
     */
    public function setSignature(Signature $signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return \LightSaml\Model\XmlDSig\Signature
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param int|string $validUntil
     *
     * @return EntitiesDescriptor
     *
     * @throws \InvalidArgumentException
     */
    public function setValidUntil($validUntil)
    {
        $value = Helper::getTimestampFromValue($validUntil);
        if ($value < 0) {
            throw new \InvalidArgumentException('Invalid validUntil');
        }
        $this->validUntil = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidUntilString()
    {
        if ($this->validUntil) {
            return Helper::time2string($this->validUntil);
        }

        return null;
    }

    /**
     * @return int
     */
    public function getValidUntilTimestamp()
    {
        return $this->validUntil;
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
     * @param EntitiesDescriptor|EntityDescriptor $item
     *
     * @return EntitiesDescriptor
     *
     * @throws \InvalidArgumentException
     */
    public function addItem($item)
    {
        if (false == $item instanceof self && false == $item instanceof EntityDescriptor) {
            throw new \InvalidArgumentException('Expected EntitiesDescriptor or EntityDescriptor');
        }
        if ($item === $this) {
            throw new \InvalidArgumentException('Circular reference detected');
        }
        if ($item instanceof self) {
            if ($item->containsItem($this)) {
                throw new \InvalidArgumentException('Circular reference detected');
            }
        }
        $this->items[] = $item;

        return $this;
    }

    /**
     * @param EntitiesDescriptor|EntityDescriptor $item
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function containsItem($item)
    {
        if (false == $item instanceof self && false == $item instanceof EntityDescriptor) {
            throw new \InvalidArgumentException('Expected EntitiesDescriptor or EntityDescriptor');
        }
        foreach ($this->items as $i) {
            if ($i === $item) {
                return true;
            }
            if ($i instanceof self) {
                if ($i->containsItem($item)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return EntitiesDescriptor[]|EntityDescriptor[]
     */
    public function getAllItems()
    {
        return $this->items;
    }

    /**
     * @return EntityDescriptor[]
     */
    public function getAllEntityDescriptors()
    {
        $result = array();
        foreach ($this->items as $item) {
            if ($item instanceof self) {
                $result = array_merge($result, $item->getAllEntityDescriptors());
            } else {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @param string $entityId
     *
     * @return EntityDescriptor|null
     */
    public function getByEntityId($entityId)
    {
        foreach ($this->getAllEntityDescriptors() as $entityDescriptor) {
            if ($entityDescriptor->getEntityID() == $entityId) {
                return $entityDescriptor;
            }
        }

        return null;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('EntitiesDescriptor', SamlConstants::NS_METADATA, $parent, $context);

        $this->attributesToXml(array('validUntil', 'cacheDuration', 'ID', 'Name'), $result);

        $this->singleElementsToXml(array('Signature'), $result, $context);

        $this->manyElementsToXml($this->getAllItems(), $result, $context);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'EntitiesDescriptor', SamlConstants::NS_METADATA);

        $this->attributesFromXml($node, array('validUntil', 'cacheDuration', 'ID', 'Name'));

        $this->singleElementsFromXml($node, $context, array(
            'Signature' => array('ds', 'LightSaml\Model\XmlDSig\SignatureXmlReader'),
        ));

        $this->manyElementsFromXml(
            $node,
            $context,
            'EntityDescriptor',
            'md',
            'LightSaml\Model\Metadata\EntityDescriptor',
            'addItem'
        );
        $this->manyElementsFromXml(
            $node,
            $context,
            'EntitiesDescriptor',
            'md',
            'LightSaml\Model\Metadata\EntitiesDescriptor',
            'addItem'
        );
    }
}
