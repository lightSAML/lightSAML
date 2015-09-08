<?php

namespace LightSaml\Credential\Criteria;

class AlgorithmCriteria implements TrustCriteriaInterface
{
    /** @var  string */
    protected $algorithm;

    /**
     * @param string $algorithm
     */
    public function __construct($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }
}
