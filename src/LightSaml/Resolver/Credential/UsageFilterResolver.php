<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Credential\Criteria\UsageCriteria;
use LightSaml\Criteria\CriteriaSet;

class UsageFilterResolver extends AbstractQueryableResolver
{
    /**
     * @param CredentialInterface[] $arrCredentials
     *
     * @return CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = [])
    {
        if (false == $criteriaSet->has(UsageCriteria::class)) {
            return $arrCredentials;
        }

        $result = [];
        foreach ($criteriaSet->get(UsageCriteria::class) as $criteria) {
            /* @var UsageCriteria $criteria */
            foreach ($arrCredentials as $credential) {
                if (false == $credential->getUsageType() || $criteria->getUsage() == $credential->getUsageType()) {
                    $result[] = $credential;
                }
            }
        }

        return $result;
    }
}
