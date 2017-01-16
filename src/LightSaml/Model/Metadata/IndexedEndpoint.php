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

class IndexedEndpoint extends Endpoint
{
    /** @var int */
    protected $index;

    /** @var bool|null */
    protected $isDefault;

    /**
     * @param bool|null $isDefault
     *
     * @return IndexedEndpoint
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = filter_var($isDefault, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE]);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIsDefaultString()
    {
        return $this->isDefault ? 'true' : 'false';
    }

    /**
     * @return bool|null
     */
    public function getIsDefaultBool()
    {
        return $this->isDefault;
    }

    /**
     * @param int $index
     *
     * @return IndexedEndpoint
     */
    public function setIndex($index)
    {
        $this->index = (int) $index;

        return $this;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param \DOMNode             $parent
     * @param SerializationContext $context
     */
    public function serialize(\DOMNode $parent, SerializationContext $context)
    {
        $this->attributesToXml(array('index', 'isDefault'), $parent);
        parent::serialize($parent, $context);
    }

    /**
     * @param \DOMNode               $node
     * @param DeserializationContext $context
     */
    public function deserialize(\DOMNode $node, DeserializationContext $context)
    {
        $this->attributesFromXml($node, array('index', 'isDefault'));

        parent::deserialize($node, $context);
    }
}
