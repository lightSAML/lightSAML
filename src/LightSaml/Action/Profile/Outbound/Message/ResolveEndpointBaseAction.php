<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Outbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Model\Metadata\EndpointReference;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Resolver\Endpoint\Criteria\BindingCriteria;
use LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria;
use LightSaml\Resolver\Endpoint\Criteria\IndexCriteria;
use LightSaml\Resolver\Endpoint\Criteria\LocationCriteria;
use LightSaml\Resolver\Endpoint\Criteria\ServiceTypeCriteria;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;

/**
 * Determines to which endpoint outbound message will be sent.
 */
abstract class ResolveEndpointBaseAction extends AbstractProfileAction
{
    /** @var EndpointResolverInterface */
    protected $endpointResolver;

    /**
     * @param LoggerInterface           $logger
     * @param EndpointResolverInterface $endpointResolver
     */
    public function __construct(LoggerInterface $logger, EndpointResolverInterface $endpointResolver)
    {
        parent::__construct($logger);

        $this->endpointResolver = $endpointResolver;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        if ($context->getEndpointContext()->getEndpoint()) {
            $this->logger->debug(
                sprintf(
                    'Endpoint already set with location "%s" and binding "%s"',
                    $context->getEndpoint()->getLocation(),
                    $context->getEndpoint()->getBinding()
                ),
                LogHelper::getActionContext($context, $this, array(
                    'endpointLocation' => $context->getEndpoint()->getLocation(),
                    'endpointBinding' => $context->getEndpoint()->getBinding(),
                ))
            );

            return;
        }

        $criteriaSet = $this->getCriteriaSet($context);

        $message = $context->getInboundContext()->getMessage();
        if ($message instanceof AuthnRequest) {
            if (null !== $message->getAssertionConsumerServiceIndex()) {
                $criteriaSet->add(new IndexCriteria($message->getAssertionConsumerServiceIndex()));
            }
            if (null !== $message->getAssertionConsumerServiceURL()) {
                $criteriaSet->add(new LocationCriteria($message->getAssertionConsumerServiceURL()));
            }
        }

        $candidates = $this->endpointResolver->resolve($criteriaSet, $context->getPartyEntityDescriptor()->getAllEndpoints());
        /** @var EndpointReference $endpointReference */
        $endpointReference = array_shift($candidates);

        if (null == $endpointReference) {
            $message = sprintf(
                "Unable to determine endpoint for entity '%s'",
                $context->getPartyEntityDescriptor()->getEntityID()
            );
            $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        $this->logger->debug(
            sprintf(
                'Endpoint resolved to location "%s" and binding "%s"',
                $endpointReference->getEndpoint()->getLocation(),
                $endpointReference->getEndpoint()->getBinding()
            ),
            LogHelper::getActionContext($context, $this, array(
                'endpointLocation' => $endpointReference->getEndpoint()->getLocation(),
                'endpointBinding' => $endpointReference->getEndpoint()->getBinding(),
            ))
        );

        $context->getEndpointContext()->setEndpoint($endpointReference->getEndpoint());
    }

    /**
     * @param ProfileContext $context
     *
     * @return CriteriaSet
     */
    protected function getCriteriaSet(ProfileContext $context)
    {
        $criteriaSet = new CriteriaSet();

        $bindings = $this->getBindings($context);
        if ($bindings) {
            $criteriaSet->add(new BindingCriteria($bindings));
        }

        $descriptorType = $this->getDescriptorType($context);
        if ($descriptorType) {
            $criteriaSet->add(new DescriptorTypeCriteria($descriptorType));
        }

        $serviceType = $this->getServiceType($context);
        if ($serviceType) {
            $criteriaSet->add(new ServiceTypeCriteria($serviceType));
        }

        return $criteriaSet;
    }

    /**
     * @param ProfileContext $context
     *
     * @return string[]
     */
    protected function getBindings(ProfileContext $context)
    {
        return array(
            SamlConstants::BINDING_SAML2_HTTP_POST,
            SamlConstants::BINDING_SAML2_HTTP_REDIRECT,
        );
    }
    /**
     * @param ProfileContext $context
     *
     * @return string|null
     */
    protected function getDescriptorType(ProfileContext $context)
    {
        return $context->getOwnRole() == ProfileContext::ROLE_IDP
            ? SpSsoDescriptor::class
            : IdpSsoDescriptor::class;
    }

    /**
     * @param ProfileContext $context
     *
     * @return string|null
     */
    protected function getServiceType(ProfileContext $context)
    {
        return;
    }
}
