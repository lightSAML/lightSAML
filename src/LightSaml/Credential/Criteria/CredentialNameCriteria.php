<?php

namespace LightSaml\Credential\Criteria;

class CredentialNameCriteria implements TrustCriteriaInterface
{
    /** @var  string */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
