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

class ServiceTypeCriteria implements CriteriaInterface
{
    /** @var string */
    protected $serviceType;

    /**
     * @param string $serviceType
     */
    public function __construct($serviceType)
    {
        $this->serviceType = $serviceType;
    }

    /**
     * @return string
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }
}
