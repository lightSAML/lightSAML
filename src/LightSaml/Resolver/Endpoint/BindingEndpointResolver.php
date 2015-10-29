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
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Resolver\Endpoint\Criteria\BindingCriteria;

class BindingEndpointResolver implements EndpointResolverInterface
{
    /**
     * @param CriteriaSet         $criteriaSet
     * @param EndpointReference[] $candidates
     *
     * @return EndpointReference[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $candidates)
    {
        if (false === $criteriaSet->has(BindingCriteria::class)) {
            return $candidates;
        }

        $arrOrdered = array();
        /** @var BindingCriteria $bindingCriteria */
        foreach ($criteriaSet->get(BindingCriteria::class) as $bindingCriteria) {
            foreach ($candidates as $endpointReference) {
                $preference = $bindingCriteria->getPreference($endpointReference->getEndpoint()->getBinding());
                if (null !== $preference) {
                    $arrOrdered[$preference][] = $endpointReference;
                }
            }
        }

        ksort($arrOrdered);

        $result = array();
        foreach ($arrOrdered as $arr) {
            foreach ($arr as $endpointReference) {
                $result[] = $endpointReference;
            }
        }

        return $result;
    }
}
