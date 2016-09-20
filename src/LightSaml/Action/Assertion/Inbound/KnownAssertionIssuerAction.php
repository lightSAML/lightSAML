<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Assertion\Inbound;

use LightSaml\Action\Assertion\AbstractAssertionAction;
use LightSaml\Context\Profile\AssertionContext;
use LightSaml\Context\Profile\Helper\LogHelper;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use Psr\Log\LoggerInterface;

class KnownAssertionIssuerAction extends AbstractAssertionAction
{
    /** @var EntityDescriptorStoreInterface */
    private $idpEntityDescriptorProvider;

    /**
     * @param LoggerInterface                $logger
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
            $message = 'Assertion element must have an issuer element';
            $this->logger->error($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        if (false == $this->idpEntityDescriptorProvider->has($context->getAssertion()->getIssuer()->getValue())) {
            $message = sprintf("Unknown issuer '%s'", $context->getAssertion()->getIssuer()->getValue());
            $this->logger->error($message, LogHelper::getActionErrorContext($context, $this, [
                'messageIssuer' => $context->getAssertion()->getIssuer()->getValue(),
            ]));
            throw new LightSamlContextException($context, $message);
        }

        $this->logger->debug(
            sprintf('Known assertion issuer: "%s"', $context->getAssertion()->getIssuer()->getValue()),
            LogHelper::getActionContext($context, $this)
        );
    }
}
