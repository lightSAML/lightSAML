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
use LightSaml\Credential\Criteria\CredentialNameCriteria;
use LightSaml\Criteria\CriteriaSet;

class CredentialNameFilterResolver extends AbstractQueryableResolver
{
    /**
     * @param CriteriaSet           $criteriaSet
     * @param CredentialInterface[] $arrCredentials
     *
     * @return CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = array())
    {
        if (false == $criteriaSet->has(CredentialNameCriteria::class)) {
            return $arrCredentials;
        }

        $result = array();
        foreach ($criteriaSet->get(CredentialNameCriteria::class) as $criteria) {
            /* @var CredentialNameCriteria $criteria */
            foreach ($arrCredentials as $credential) {
                $arrCredentialNames = $credential->getKeyNames();
                $criteriaName = $criteria->getName();
                foreach ($arrCredentialNames as $credentialName) {
                    if ($credentialName == $criteriaName) {
                        $result[] = $credential;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
