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
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Error\LightSamlContextException;
use LightSaml\Meta\TrustOptions\TrustOptions;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;
use LightSaml\Store\TrustOptions\TrustOptionsStoreInterface;
use Psr\Log\LoggerInterface;

/**
 * Looks up inbound message Issuer in entity descriptor providers and sets it to the party context.
 */
class ResolvePartyEntityIdAction extends AbstractProfileAction
{
    /** @var EntityDescriptorStoreInterface */
    private $spEntityDescriptorProvider;

    /** @var EntityDescriptorStoreInterface */
    private $idpEntityDescriptorProvider;

    /** @var TrustOptionsStoreInterface */
    protected $trustOptionsProvider;

    /**
     * @param LoggerInterface                $logger
     * @param EntityDescriptorStoreInterface $spEntityDescriptorProvider
     * @param EntityDescriptorStoreInterface $idpEntityDescriptorProvider
     * @param TrustOptionsStoreInterface     $trustOptionsProvider
     */
    public function __construct(
        LoggerInterface $logger,
        EntityDescriptorStoreInterface $spEntityDescriptorProvider,
        EntityDescriptorStoreInterface $idpEntityDescriptorProvider,
        TrustOptionsStoreInterface $trustOptionsProvider
    ) {
        parent::__construct($logger);

        $this->spEntityDescriptorProvider = $spEntityDescriptorProvider;
        $this->idpEntityDescriptorProvider = $idpEntityDescriptorProvider;
        $this->trustOptionsProvider = $trustOptionsProvider;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $partyContext = $context->getPartyEntityContext();

        if ($partyContext->getEntityDescriptor() && $partyContext->getTrustOptions()) {
            $this->logger->debug(
                sprintf('Party EntityDescriptor and TrustOptions already set for "%s"', $partyContext->getEntityDescriptor()->getEntityID()),
                LogHelper::getActionContext($context, $this, array(
                    'partyEntityId' => $partyContext->getEntityDescriptor()->getEntityID(),
                ))
            );

            return;
        }

        $entityId = $partyContext->getEntityDescriptor() ? $partyContext->getEntityDescriptor()->getEntityID() : null;
        $entityId = $entityId ? $entityId : $partyContext->getEntityId();
        if (null == $entityId) {
            $message = 'EntityID is not set in the party context';
            $this->logger->critical($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        if (null == $partyContext->getEntityDescriptor()) {
            $partyEntityDescriptor = $this->getPartyEntityDescriptor(
                $context,
                $context->getOwnRole() === ProfileContext::ROLE_IDP
                ? $this->spEntityDescriptorProvider
                : $this->idpEntityDescriptorProvider,
                $context->getPartyEntityContext()->getEntityId()
            );
            $partyContext->setEntityDescriptor($partyEntityDescriptor);
            $this->logger->debug(
                sprintf('Known issuer resolved: "%s"', $partyEntityDescriptor->getEntityID()),
                LogHelper::getActionContext($context, $this, array(
                    'partyEntityId' => $partyEntityDescriptor->getEntityID(),
                ))
            );
        }

        if (null == $partyContext->getTrustOptions()) {
            $trustOptions = $this->trustOptionsProvider->get($partyContext->getEntityDescriptor()->getEntityID());
            if (null === $trustOptions) {
                $trustOptions = new TrustOptions();
            }
            $partyContext->setTrustOptions($trustOptions);
        }
    }

    /**
     * @param ProfileContext                 $context
     * @param EntityDescriptorStoreInterface $entityDescriptorProvider
     * @param string                         $entityId
     *
     * @return \LightSaml\Model\Metadata\EntityDescriptor
     */
    protected function getPartyEntityDescriptor(
        ProfileContext $context,
        EntityDescriptorStoreInterface $entityDescriptorProvider,
        $entityId
    ) {
        $partyEntityDescriptor = $entityDescriptorProvider->get($entityId);
        if (null === $partyEntityDescriptor) {
            $message = sprintf("Unknown issuer '%s'", $entityId);
            $this->logger->emergency($message, LogHelper::getActionErrorContext($context, $this));
            throw new LightSamlContextException($context, $message);
        }

        return $partyEntityDescriptor;
    }
}
