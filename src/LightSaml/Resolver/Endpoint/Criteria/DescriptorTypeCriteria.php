<?php

namespace LightSaml\Resolver\Endpoint\Criteria;

use LightSaml\Criteria\CriteriaInterface;

class DescriptorTypeCriteria implements CriteriaInterface
{
    /** @var  string */
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
