<?php

namespace LightSaml\Resolver\Endpoint;

use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\EndpointReference;

interface EndpointResolverInterface
{
    /**
     * @param CriteriaSet         $criteriaSet
     * @param EndpointReference[] $candidates
     *
     * @return EndpointReference[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $candidates);
}
