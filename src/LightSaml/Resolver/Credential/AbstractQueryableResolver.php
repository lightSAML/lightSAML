<?php

namespace LightSaml\Resolver\Credential;

abstract class AbstractQueryableResolver implements CredentialResolverInterface
{
    /**
     * @return CredentialResolverQuery
     */
    public function query()
    {
        return new CredentialResolverQuery($this);
    }
}
