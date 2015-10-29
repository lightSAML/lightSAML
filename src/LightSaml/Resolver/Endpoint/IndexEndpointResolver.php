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
use LightSaml\Model\Metadata\IndexedEndpoint;
use LightSaml\Resolver\Endpoint\Criteria\IndexCriteria;
use LightSaml\Criteria\CriteriaSet;

class IndexEndpointResolver implements EndpointResolverInterface
{
    /**
     * @param CriteriaSet         $criteriaSet
     * @param EndpointReference[] $candidates
     *
     * @return EndpointReference[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $candidates)
    {
        if (false === $criteriaSet->has(IndexCriteria::class)) {
            return $candidates;
        }

        $result = array();
        /** @var IndexCriteria $indexCriteria */
        foreach ($criteriaSet->get(IndexCriteria::class) as $indexCriteria) {
            foreach ($candidates as $endpointReference) {
                $endpoint = $endpointReference->getEndpoint();
                if ($endpoint instanceof IndexedEndpoint) {
                    if ($endpoint->getIndex() == $indexCriteria->getIndex()) {
                        $result[] = $endpointReference;
                    }
                }
            }
        }

        return $result;
    }
}
