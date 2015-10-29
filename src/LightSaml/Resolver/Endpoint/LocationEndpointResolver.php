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

use LightSaml\Model\Metadata\EndpointReference;
use LightSaml\Resolver\Endpoint\Criteria\LocationCriteria;
use LightSaml\Criteria\CriteriaSet;

class LocationEndpointResolver implements EndpointResolverInterface
{
    /**
     * @param CriteriaSet         $criteriaSet
     * @param EndpointReference[] $candidates
     *
     * @return EndpointReference[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $candidates)
    {
        if (false === $criteriaSet->has(LocationCriteria::class)) {
            return $candidates;
        }

        $result = array();
        /** @var LocationCriteria $locationCriteria */
        foreach ($criteriaSet->get(LocationCriteria::class) as $locationCriteria) {
            foreach ($candidates as $endpointReference) {
                if ($endpointReference->getEndpoint()->getLocation() == $locationCriteria->getLocation()) {
                    $result[] = $endpointReference;
                }
            }
        }

        return $result;
    }
}
