<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Resolver\Credential\Factory;

use LightSaml\Resolver\Credential\AlgorithmFilterResolver;
use LightSaml\Resolver\Credential\CompositeFilterResolver;
use LightSaml\Resolver\Credential\CredentialNameFilterResolver;
use LightSaml\Resolver\Credential\CredentialResolverInterface;
use LightSaml\Resolver\Credential\EntityIdResolver;
use LightSaml\Resolver\Credential\MetadataFilterResolver;
use LightSaml\Resolver\Credential\PrivateKeyResolver;
use LightSaml\Resolver\Credential\PublicKeyThumbprintResolver;
use LightSaml\Resolver\Credential\UsageFilterResolver;
use LightSaml\Resolver\Credential\X509CredentialResolver;
use LightSaml\Store\Credential\CredentialStoreInterface;

class CredentialResolverFactory
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
     * @return CredentialResolverInterface
     */
    public function build()
    {
        $result = (new CompositeFilterResolver())
            ->add(new EntityIdResolver($this->credentialStore))
            ->add(new AlgorithmFilterResolver())
            ->add(new CredentialNameFilterResolver())
            ->add(new MetadataFilterResolver())
            ->add(new UsageFilterResolver())
            ->add(new PrivateKeyResolver())
            ->add(new X509CredentialResolver())
            ->add(new PublicKeyThumbprintResolver())
        ;

        return $result;
    }
}
