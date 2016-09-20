<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria;
use LightSaml\Resolver\Endpoint\Criteria\LocationCriteria;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractDestinationValidatorAction extends AbstractProfileAction
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
     *
     * @return void
     */
    protected function doExecute(ProfileContext $context)
    {
        $message = MessageContextHelper::asSamlMessage($context->getInboundContext());
        $destination = $message->getDestination();

        if (null == $destination) {
            return;
        }

        $criteriaSet = $this->getCriteriaSet($context, $destination);
        $endpoints = $this->endpointResolver->resolve($criteriaSet, $context->getOwnEntityDescriptor()->getAllEndpoints());

        if ($endpoints) {
            return;
        }

        $message = sprintf('Invalid inbound message destination "%s"', $destination);
        $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
        throw new LightSamlContextException($context, $message);
    }

    /**
     * @param ProfileContext $context
     * @param string         $location
     *
     * @return CriteriaSet
     */
    protected function getCriteriaSet(ProfileContext $context, $location)
    {
        $criteriaSet = new CriteriaSet([
            new DescriptorTypeCriteria(
                $context->getOwnRole() === ProfileContext::ROLE_IDP
                ? IdpSsoDescriptor::class
                : SpSsoDescriptor::class
            ),
            new LocationCriteria($location),
        ]);

        return $criteriaSet;
    }
}
