<?php

namespace LightSaml\Action\Profile\Inbound\Message;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Criteria\CriteriaSet;
use LightSaml\Error\LightSamlValidationException;
use LightSaml\Model\Metadata\IdpSsoDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\Resolver\Endpoint\Criteria\DescriptorTypeCriteria;
use LightSaml\Resolver\Endpoint\EndpointResolverInterface;
use Psr\Log\LoggerInterface;

abstract class DestinationValidatorBaseAction extends AbstractProfileAction
{
    /** @var  EndpointResolverInterface */
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

        $criteriaSet = $this->getCriteriaSet($context);
        $endpoints = $this->endpointResolver->resolve($criteriaSet, $context->getOwnEntityDescriptor()->getAllEndpoints());

        if ($endpoints) {
            return;
        }

        $message = sprintf('Invalid inbound message destination "%s"', $destination);
        $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
        throw new LightSamlValidationException($message);
    }

    protected function getCriteriaSet(ProfileContext $context)
    {
        $criteriaSet = new CriteriaSet(array(
            new DescriptorTypeCriteria(
                $context->getOwnRole() === ProfileContext::ROLE_IDP
                ? IdpSsoDescriptor::class
                : SpSsoDescriptor::class
            ),
        ));

        return $criteriaSet;
    }
}
