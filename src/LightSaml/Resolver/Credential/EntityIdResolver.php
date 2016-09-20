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
use LightSaml\Store\Credential\CredentialStoreInterface;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Credential\Criteria\EntityIdCriteria;

class EntityIdResolver extends AbstractQueryableResolver
{
    /** @var CredentialStoreInterface */
    protected $credentialStore;

    /**
     * @param CredentialStoreInterface $credentialStore
     */
    public function __construct(CredentialStoreInterface $credentialStore)
    {
        $this->credentialStore = $credentialStore;
    }

    /**
     * @param CriteriaSet                 $criteriaSet
     * @param array|CredentialInterface[] $arrCredentials
     *
     * @return array|CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = array())
    {
        $result = array();
        foreach ($criteriaSet->get(EntityIdCriteria::class) as $criteria) {
            /* @var EntityIdCriteria $criteria */
            $result = array_merge($result, $this->credentialStore->getByEntityId($criteria->getEntityId()));
        }

        return $result;
    }
}
