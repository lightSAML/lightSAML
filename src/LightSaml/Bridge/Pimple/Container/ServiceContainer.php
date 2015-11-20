<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Bridge\Pimple\Container;

use LightSaml\Binding\BindingFactoryInterface;
use LightSaml\Build\Container\ServiceContainerInterface;
use LightSaml\Resolver\Credential\CredentialResolverInterface;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use LightSaml\Logout\Resolver\Logout\LogoutSessionResolverInterface;
use LightSaml\Resolver\Session\SessionProcessorInterface;
use LightSaml\Resolver\Signature\SignatureResolverInterface;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidator;
use LightSaml\Validator\Model\Assertion\AssertionValidatorInterface;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;
use LightSaml\Validator\Model\Signature\SignatureValidatorInterface;

class ServiceContainer extends AbstractPimpleContainer implements ServiceContainerInterface
{
    const ASSERTION_VALIDATOR = 'lightsaml.container.assertion_validator';
    const ASSERTION_TIME_VALIDATOR = 'lightsaml.container.assertion_time_validator';
    const SIGNATURE_RESOLVER = 'lightsaml.container.signature_resolver';
    const ENDPOINT_RESOLVER = 'lightsaml.container.endpoint_resolver';
    const NAME_ID_VALIDATOR = 'lightsaml.container.name_id_validator';
    const BINDING_FACTORY = 'lightsaml.container.binding_factory';
    const SIGNATURE_VALIDATOR = 'lightsaml.container.signature_validator';
    const CREDENTIAL_RESOLVER = 'lightsaml.container.credential_resolver';
    const LOGOUT_SESSION_RESOLVER = 'lightsaml.container.logout_session_resolver';
    const SESSION_PROCESSOR = 'lightsaml.container.session_processor';

    /**
     * @return AssertionValidatorInterface
     */
    public function getAssertionValidator()
    {
        return $this->pimple[self::ASSERTION_VALIDATOR];
    }

    /**
     * @return AssertionTimeValidator
     */
    public function getAssertionTimeValidator()
    {
        return $this->pimple[self::ASSERTION_TIME_VALIDATOR];
    }

    /**
     * @return SignatureResolverInterface
     */
    public function getSignatureResolver()
    {
        return $this->pimple[self::SIGNATURE_RESOLVER];
    }

    /**
     * @return EndpointResolverInterface
     */
    public function getEndpointResolver()
    {
        return $this->pimple[self::ENDPOINT_RESOLVER];
    }

    /**
     * @return NameIdValidatorInterface
     */
    public function getNameIdValidator()
    {
        return $this->pimple[self::NAME_ID_VALIDATOR];
    }

    /**
     * @return BindingFactoryInterface
     */
    public function getBindingFactory()
    {
        return $this->pimple[self::BINDING_FACTORY];
    }

    /**
     * @return SignatureValidatorInterface
     */
    public function getSignatureValidator()
    {
        return $this->pimple[self::SIGNATURE_VALIDATOR];
    }

    /**
     * @return CredentialResolverInterface
     */
    public function getCredentialResolver()
    {
        return $this->pimple[self::CREDENTIAL_RESOLVER];
    }

    /**
     * @return LogoutSessionResolverInterface
     */
    public function getLogoutSessionResolver()
    {
        return $this->pimple[self::LOGOUT_SESSION_RESOLVER];
    }

    /**
     * @return SessionProcessorInterface
     */
    public function getSessionProcessor()
    {
        return $this->pimple[self::SESSION_PROCESSOR];
    }
}
