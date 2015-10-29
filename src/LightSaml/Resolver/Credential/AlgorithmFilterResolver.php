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
use LightSaml\Credential\Criteria\AlgorithmCriteria;
use LightSaml\Criteria\CriteriaSet;

class AlgorithmFilterResolver extends AbstractQueryableResolver
{
    /**
     * @param CriteriaSet           $criteriaSet
     * @param CredentialInterface[] $arrCredentials
     *
     * @return CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = array())
    {
        if (false == $criteriaSet->has(AlgorithmCriteria::class)) {
            return $arrCredentials;
        }

        $result = array();
        foreach ($criteriaSet->get(AlgorithmCriteria::class) as $criteria) {
            /* @var AlgorithmCriteria $criteria */
            foreach ($arrCredentials as $credential) {
                if (($credential->getPrivateKey() && $credential->getPrivateKey()->getAlgorith() == $criteria->getAlgorithm()) ||
                    ($credential->getPublicKey() && $credential->getPublicKey()->getAlgorith() == $criteria->getAlgorithm())
                ) {
                    $result[] = $credential;
                }
            }
        }

        return $result;
    }
}
