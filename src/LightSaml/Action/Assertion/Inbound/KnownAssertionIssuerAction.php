<?php

namespace LightSaml\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Error\LightSamlValidationException;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use Psr\Log\LoggerInterface;

class KnownAssertionIssuerAction extends AbstractAssertionAction
{
    /** @var  EntityDescriptorStoreInterface */
    private $idpEntityDescriptorProvider;

    /**
     * @param LoggerInterface                   $logger
     * @param EntityDescriptorStoreInterface $idpEntityDescriptorProvider
     */
    public function __construct(LoggerInterface $logger, EntityDescriptorStoreInterface $idpEntityDescriptorProvider)
    {
        parent::__construct($logger);

        $this->idpEntityDescriptorProvider = $idpEntityDescriptorProvider;
    }

    /**
     * @param AssertionContext $context
     *
     * @return void
     */
    protected function doExecute(AssertionContext $context)
    {
        if (null === $context->getAssertion()->getIssuer()) {
            throw new LightSamlValidationException('Assertion element must have an issuer element.');
        }

        if (false === $this->idpEntityDescriptorProvider->has($context->getAssertion()->getIssuer()->getValue())) {
            throw new LightSamlValidationException(sprintf("Unknown issuer '%s'", $context->getAssertion()->getIssuer()->getValue()));
        }

        $this->logger->debug(
            sprintf('Known assertion issuer: "%s"', $context->getAssertion()->getIssuer()->getValue()),
            LogHelper::getActionContext($context, $this)
        );
    }
}
