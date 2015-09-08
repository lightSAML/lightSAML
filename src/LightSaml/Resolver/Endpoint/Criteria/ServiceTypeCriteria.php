<?php

namespace LightSaml\Resolver\Endpoint\Criteria;

use LightSaml\Criteria\CriteriaInterface;

class ServiceTypeCriteria implements CriteriaInterface
{
    /** @var  string */
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
