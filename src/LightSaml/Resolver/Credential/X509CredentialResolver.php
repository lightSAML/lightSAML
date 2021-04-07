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
use LightSaml\Credential\Criteria\X509CredentialCriteria;
use LightSaml\Credential\X509CredentialInterface;
use LightSaml\Criteria\CriteriaSet;

class X509CredentialResolver extends AbstractQueryableResolver
{
    /**
     * @param CredentialInterface[] $arrCredentials
     *
     * @return CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = [])
    {
        if (false == $criteriaSet->has(X509CredentialCriteria::class)) {
            return $arrCredentials;
        }

        $result = [];
        foreach ($arrCredentials as $credential) {
            if ($credential instanceof X509CredentialInterface) {
                $result[] = $credential;
            }
        }

        return $result;
    }
}
