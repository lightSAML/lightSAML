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

abstract class AbstractCompositeResolver extends AbstractQueryableResolver
{
    /** @var CredentialResolverInterface[] */
    protected $resolvers = array();

    /**
     * @param CredentialResolverInterface $resolver
     *
     * @return AbstractCompositeResolver
     */
    public function add(CredentialResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;

        return $this;
    }
}
