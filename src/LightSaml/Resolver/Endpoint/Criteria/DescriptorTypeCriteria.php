<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Endpoint\Criteria;

use LightSaml\Criteria\CriteriaInterface;

class DescriptorTypeCriteria implements CriteriaInterface
{
    /** @var string */
    protected $descriptorType;

    /**
     * @param string $descriptorType
     */
    public function __construct($descriptorType)
    {
        $this->descriptorType = $descriptorType;
    }

    /**
     * @return string
     */
    public function getDescriptorType()
    {
        return $this->descriptorType;
    }
}
