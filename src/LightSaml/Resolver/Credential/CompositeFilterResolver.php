<?php

namespace LightSaml\Resolver\Credential;

use LightSaml\Credential\CredentialInterface;
use LightSaml\Criteria\CriteriaSet;

class CompositeFilterResolver extends AbstractCompositeResolver
{
    /**
     * @param CriteriaSet           $criteriaSet
     * @param CredentialInterface[] $arrCredentials
     *
     * @return CredentialInterface[]
     */
    public function resolve(CriteriaSet $criteriaSet, array $arrCredentials = array())
    {
        $result = $arrCredentials;
        foreach ($this->resolvers as $resolver) {
            $result = $resolver->resolve($criteriaSet, $result);
        }

        return $result;
    }
}
