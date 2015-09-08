<?php

namespace LightSaml\Resolver\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Credential\Criteria\PrivateKeyCriteria;

class PrivateKeyResolver extends AbstractQueryableResolver
{
    /**
     * @param CriteriaSet           $criteriaSet
     * @param CredentialInterface[] $arrCredentials
     *
     * @return CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = array())
    {
        if (false == $criteriaSet->has(PrivateKeyCriteria::class)) {
            return $arrCredentials;
        }

        $result = array();
        foreach ($arrCredentials as $credential) {
            if ($credential->getPrivateKey()) {
                $result[] = $credential;
            }
        }

        return $result;
    }
}
