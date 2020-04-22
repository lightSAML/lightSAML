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
use LightSaml\Credential\Criteria\EntityIdCriteria;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Store\Credential\CredentialStoreInterface;

class EntityIdResolver extends AbstractQueryableResolver
{
    /** @var CredentialStoreInterface */
    protected $credentialStore;

    public function __construct(CredentialStoreInterface $credentialStore)
    {
        $this->credentialStore = $credentialStore;
    }

    /**
     * @param array|CredentialInterface[] $arrCredentials
     *
     * @return array|CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = [])
    {
        $result = [];
        foreach ($criteriaSet->get(EntityIdCriteria::class) as $criteria) {
            /* @var EntityIdCriteria $criteria */
            $result = array_merge($result, $this->credentialStore->getByEntityId($criteria->getEntityId()));
        }

        return $result;
    }
}
