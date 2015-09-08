<?php

namespace LightSaml\Credential\Criteria;

class UsageCriteria implements TrustCriteriaInterface
{
    /** @var string */
    protected $usage;

    /**
     * @param string $usage
     */
    public function __construct($usage)
    {
        $this->usage = $usage;
    }

    /**
     * @return string
     */
    public function getUsage()
    {
        return $this->usage;
    }
}
