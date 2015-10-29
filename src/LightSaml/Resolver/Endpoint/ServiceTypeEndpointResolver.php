<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Endpoint;

use LightSaml\Criteria\CriteriaSet;
use LightSaml\Model\Metadata\EndpointReference;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;

/**
 * Filters out those endpoint candidates which are not an instance of the type
 * specified in the ServiceTypeCriteria. If criteria set does not have
 * ServiceTypeCriteria it will return all endpoint candidates.
 */
class ServiceTypeEndpointResolver implements EndpointResolverInterface
{
    /**
     * @param CriteriaSet         $criteriaSet
     * @param EndpointReference[] $candidates
     *
     * @return EndpointReference[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $candidates)
    {
        if (false === $criteriaSet->has(ServiceTypeCriteria::class)) {
            return $candidates;
        }

        $result = array();
        /** @var ServiceTypeCriteria $serviceTypeCriteria */
        foreach ($criteriaSet->get(ServiceTypeCriteria::class) as $serviceTypeCriteria) {
            foreach ($candidates as $endpointReference) {
                $type = $serviceTypeCriteria->getServiceType();
                if ($endpointReference->getEndpoint() instanceof $type) {
                    $result[] = $endpointReference;
                }
            }
        }

        return $result;
    }
}
