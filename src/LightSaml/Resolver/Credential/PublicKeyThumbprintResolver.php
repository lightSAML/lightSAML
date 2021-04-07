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
use LightSaml\Credential\Criteria\PublicKeyThumbprintCriteria;
use LightSaml\Criteria\CriteriaSet;

class PublicKeyThumbprintResolver extends AbstractQueryableResolver
{
    /**
     * @param CredentialInterface[] $arrCredentials
     *
     * @return CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = [])
    {
        if (false == $criteriaSet->has(PublicKeyThumbprintCriteria::class)) {
            return $arrCredentials;
        }

        $result = [];
        /** @var PublicKeyThumbprintCriteria $criteria */
        foreach ($criteriaSet->get(PublicKeyThumbprintCriteria::class) as $criteria) {
            foreach ($arrCredentials as $credential) {
                if ($credential->getPublicKey() && $credential->getPublicKey()->getX509Thumbprint() == $criteria->getThumbprint()) {
                    $result[] = $credential;
                }
            }
        }

        return $result;
    }
}
