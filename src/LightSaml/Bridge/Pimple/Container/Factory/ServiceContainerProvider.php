<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Bridge\Pimple\Container\Factory;

use LightSaml\Binding\BindingFactory;
use LightSaml\Bridge\Pimple\Container\ServiceContainer;
use LightSaml\Build\Container\CredentialContainerInterface;
use LightSaml\Build\Container\StoreContainerInterface;
use LightSaml\Build\Container\SystemContainerInterface;
use LightSaml\Resolver\Credential\Factory\CredentialResolverFactory;
use LightSaml\Resolver\Endpoint\BindingEndpointResolver;
use LightSaml\Resolver\Endpoint\CompositeEndpointResolver;
use LightSaml\Resolver\Endpoint\DescriptorTypeEndpointResolver;
use LightSaml\Resolver\Endpoint\IndexEndpointResolver;
use LightSaml\Resolver\Endpoint\LocationEndpointResolver;
use LightSaml\Resolver\Endpoint\ServiceTypeEndpointResolver;
use LightSaml\Resolver\Logout\LogoutSessionResolver;
use LightSaml\Resolver\Session\SessionProcessor;
use LightSaml\Resolver\Signature\OwnSignatureResolver;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidator;
use LightSaml\Validator\Model\Assertion\AssertionValidator;
use LightSaml\Validator\Model\NameId\NameIdValidator;
use LightSaml\Validator\Model\Signature\SignatureValidator;
use LightSaml\Validator\Model\Statement\StatementValidator;
use LightSaml\Validator\Model\Subject\SubjectValidator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceContainerProvider implements ServiceProviderInterface
{
    /** @var CredentialContainerInterface */
    private $credentialContainer;

    /** @var SystemContainerInterface */
    private $systemContainer;

    /** @var StoreContainerInterface */
    private $storeContainer;

    /**
     * @param CredentialContainerInterface $credentialContainer
     * @param StoreContainerInterface      $storeContainer
     * @param SystemContainerInterface     $systemContainer
     */
    public function __construct(
        CredentialContainerInterface $credentialContainer,
        StoreContainerInterface $storeContainer,
        SystemContainerInterface $systemContainer
    ) {
        $this->credentialContainer = $credentialContainer;
        $this->storeContainer = $storeContainer;
        $this->systemContainer = $systemContainer;
    }

    /**
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple[ServiceContainer::NAME_ID_VALIDATOR] = function () {
            return new NameIdValidator();
        };

        $pimple[ServiceContainer::ASSERTION_TIME_VALIDATOR] = function () {
            return new AssertionTimeValidator();
        };

        $pimple[ServiceContainer::ASSERTION_VALIDATOR] = function (Container $c) {
            $nameIdValidator = $c[ServiceContainer::NAME_ID_VALIDATOR];

            return new AssertionValidator(
                $nameIdValidator,
                new SubjectValidator($nameIdValidator),
                new StatementValidator()
            );
        };

        $pimple[ServiceContainer::ENDPOINT_RESOLVER] = function () {
            return new CompositeEndpointResolver(array(
                new BindingEndpointResolver(),
                new DescriptorTypeEndpointResolver(),
                new ServiceTypeEndpointResolver(),
                new IndexEndpointResolver(),
                new LocationEndpointResolver(),
            ));
        };

        $pimple[ServiceContainer::BINDING_FACTORY] = function () {
            return new BindingFactory($this->systemContainer->getEventDispatcher());
        };

        $pimple[ServiceContainer::CREDENTIAL_RESOLVER] = function () {
            $factory = new CredentialResolverFactory($this->credentialContainer->getCredentialStore());

            return $factory->build();
        };

        $pimple[ServiceContainer::SIGNATURE_RESOLVER] = function (Container $c) {
            $credentialResolver = $c[ServiceContainer::CREDENTIAL_RESOLVER];

            return new OwnSignatureResolver($credentialResolver);
        };

        $pimple[ServiceContainer::SIGNATURE_VALIDATOR] = function (Container $c) {
            $credentialResolver = $c[ServiceContainer::CREDENTIAL_RESOLVER];

            return new SignatureValidator($credentialResolver);
        };

        $pimple[ServiceContainer::LOGOUT_SESSION_RESOLVER] = function () {
            return new LogoutSessionResolver($this->storeContainer->getSsoStateStore());
        };

        $pimple[ServiceContainer::SESSION_PROCESSOR] = function () {
            return new SessionProcessor($this->storeContainer->getSsoStateStore(), $this->systemContainer->getTimeProvider());
        };
    }
}
