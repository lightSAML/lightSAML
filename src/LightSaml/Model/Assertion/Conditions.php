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
use LightSaml\SamlConstants;

class Conditions extends AbstractSamlModel
{
    /**
     * @var int|null
     */
    protected $notBefore;

    /**
     * @var int|null
     */
    protected $notOnOrAfter;

    /**
     * @var array|AbstractCondition[]|AudienceRestriction[]|OneTimeUse[]|ProxyRestriction[]
     */
    protected $items = array();

    /**
     * @param AbstractCondition $item
     *
     * @return Conditions
     */
    public function addItem(AbstractCondition $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @return AbstractCondition[]|AudienceRestriction[]|OneTimeUse[]|ProxyRestriction[]|array
     */
    public function getAllItems()
    {
        return $this->items;
    }

    /**
     * @return \LightSaml\Model\Assertion\AudienceRestriction[]
     */
    public function getAllAudienceRestrictions()
    {
        $result = array();
        foreach ($this->items as $item) {
            if ($item instanceof AudienceRestriction) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return \LightSaml\Model\Assertion\AudienceRestriction|null
     */
    public function getFirstAudienceRestriction()
    {
        foreach ($this->items as $item) {
            if ($item instanceof AudienceRestriction) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return \LightSaml\Model\Assertion\OneTimeUse[]
     */
    public function getAllOneTimeUses()
    {
        $result = array();
        foreach ($this->items as $item) {
            if ($item instanceof OneTimeUse) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return \LightSaml\Model\Assertion\OneTimeUse|null
     */
    public function getFirstOneTimeUse()
    {
        foreach ($this->items as $item) {
            if ($item instanceof OneTimeUse) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return \LightSaml\Model\Assertion\ProxyRestriction[]
     */
    public function getAllProxyRestrictions()
    {
        $result = array();
        foreach ($this->items as $item) {
            if ($item instanceof ProxyRestriction) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return \LightSaml\Model\Assertion\ProxyRestriction|null
     */
    public function getFirstProxyRestriction()
    {
        foreach ($this->items as $item) {
            if ($item instanceof ProxyRestriction) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param int|string|\DateTime|null $notBefore
     *
     * @return Conditions
     */
    public function setNotBefore($notBefore)
    {
        $this->notBefore = Helper::getTimestampFromValue($notBefore);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNotBeforeTimestamp()
    {
        return $this->notBefore;
    }

    /**
     * @return int|null
     */
    public function getNotBeforeString()
    {
        if ($this->notBefore) {
            return Helper::time2string($this->notBefore);
        }

        return null;
    }

    /**
     * @return \DateTime|null
     */
    public function getNotBeforeDateTime()
    {
        if ($this->notBefore) {
            return new \DateTime('@'.$this->notBefore);
        }

        return null;
    }

    /**
     * @param int|null $notOnOrAfter
     *
     * @return Conditions
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
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     *
     * @return void
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $result = $this->createElement('Conditions', SamlConstants::NS_ASSERTION, $parent, $context);

        $this->attributesToXml(
            array('NotBefore', 'NotOnOrAfter'),
            $result
        );

        foreach ($this->items as $item) {
            $item->serialize($result, $context);
        }
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->checkXmlNodeName($node, 'Conditions', SamlConstants::NS_ASSERTION);

        $this->attributesFromXml($node, array('NotBefore', 'NotOnOrAfter'));

        $this->manyElementsFromXml(
            $node,
            $context,
            'AudienceRestriction',
            'saml',
            'LightSaml\Model\Assertion\AudienceRestriction',
            'addItem'
        );
        $this->manyElementsFromXml(
            $node,
            $context,
            'OneTimeUse',
            'saml',
            'LightSaml\Model\Assertion\OneTimeUse',
            'addItem'
        );
        $this->manyElementsFromXml(
            $node,
            $context,
            'ProxyRestriction',
            'saml',
            'LightSaml\Model\Assertion\ProxyRestriction',
            'addItem'
        );
    }
}
