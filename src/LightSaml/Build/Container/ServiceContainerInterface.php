<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Build\Container;

use LightSaml\Binding\BindingFactoryInterface;
use LightSaml\Resolver\Credential\CredentialResolverInterface;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use LightSaml\Logout\Resolver\Logout\LogoutSessionResolverInterface;
use LightSaml\Resolver\Session\SessionProcessorInterface;
use LightSaml\Resolver\Signature\SignatureResolverInterface;
use LightSaml\Validator\Model\Assertion\AssertionTimeValidator;
use LightSaml\Validator\Model\Assertion\AssertionValidatorInterface;
use LightSaml\Validator\Model\NameId\NameIdValidatorInterface;
use LightSaml\Validator\Model\Signature\SignatureValidatorInterface;

interface ServiceContainerInterface
{
    /**
     * @return AssertionValidatorInterface
     */
    public function getAssertionValidator();

    /**
     * @return AssertionTimeValidator
     */
    public function getAssertionTimeValidator();

    /**
     * @return SignatureResolverInterface
     */
    public function getSignatureResolver();

    /**
     * @return EndpointResolverInterface
     */
    public function getEndpointResolver();

    /**
     * @return NameIdValidatorInterface
     */
    public function getNameIdValidator();

    /**
     * @return BindingFactoryInterface
     */
    public function getBindingFactory();

    /**
     * @return SignatureValidatorInterface
     */
    public function getSignatureValidator();

    /**
     * @return CredentialResolverInterface
     */
    public function getCredentialResolver();

    /**
     * @return LogoutSessionResolverInterface
     */
    public function getLogoutSessionResolver();

    /**
     * @return SessionProcessorInterface
     */
    public function getSessionProcessor();
}
