<?php

/*
 * This file is part of the LightSAML-Core package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Action\Profile\Inbound\Response;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Resolver\Session\SessionProcessorInterface;
use Psr\Log\LoggerInterface;

class SpSsoStateAction extends AbstractProfileAction
{
    /** @var SessionProcessorInterface */
    private $sessionProcessor;

    public function __construct(LoggerInterface $logger, SessionProcessorInterface $sessionProcessor)
    {
        parent::__construct($logger);

        $this->sessionProcessor = $sessionProcessor;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $response = MessageContextHelper::asResponse($context->getInboundContext());

        $this->sessionProcessor->processAssertions(
            $response->getAllAssertions(),
            $context->getOwnEntityDescriptor()->getEntityID(),
            $context->getPartyEntityDescriptor()->getEntityID()
        );
    }
}
