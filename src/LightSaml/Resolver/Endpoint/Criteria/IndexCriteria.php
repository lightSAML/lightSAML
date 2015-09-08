<?php

namespace LightSaml\Resolver\Endpoint\Criteria;

use LightSaml\Criteria\CriteriaInterface;

class IndexCriteria implements CriteriaInterface
{
    /** @var  string */
    protected $index;

    /**
     * @param string $index
     */
    public function __construct($index)
    {
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }
}
