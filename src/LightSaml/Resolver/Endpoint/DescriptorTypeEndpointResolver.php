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
use LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria;

/**
 * Filters out candidate endpoints which RoleDescriptor does not match with type specified
 * in the DescriptorTypeCriteria. If criteria set does not have DescriptorTypeCriteria
 * it will return all endpoint candidates.
 */
class DescriptorTypeEndpointResolver implements EndpointResolverInterface
{
    /**
     * @param CriteriaSet         $criteriaSet
     * @param EndpointReference[] $candidates
     *
     * @return EndpointReference[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $candidates)
    {
        if (false === $criteriaSet->has(DescriptorTypeCriteria::class)) {
            return $candidates;
        }

        $result = array();
        /** @var DescriptorTypeCriteria $descriptorTypeCriteria */
        foreach ($criteriaSet->get(DescriptorTypeCriteria::class) as $descriptorTypeCriteria) {
            foreach ($candidates as $endpointReference) {
                $type = $descriptorTypeCriteria->getDescriptorType();
                if ($endpointReference->getDescriptor() instanceof $type) {
                    $result[] = $endpointReference;
                }
            }
        }

        return $result;
    }
}
