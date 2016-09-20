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
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use Psr\Log\LoggerInterface;

/**
 * Sets the Version of the outbound message to the given value.
 */
class MessageVersionAction extends AbstractProfileAction
{
    /** @var string */
    private $version;

    /**
     * @param LoggerInterface $logger
     * @param string          $version
     */
    public function __construct(LoggerInterface $logger, $version)
    {
        parent::__construct($logger);

        $this->version = $version;
    }

    /**
     * @param ProfileContext $context
     *
     * @return void
     */
    protected function doExecute(ProfileContext $context)
    {
        MessageContextHelper::asSamlMessage($context->getOutboundContext())
            ->setVersion($this->version);

        $this->logger->debug(
            sprintf('Message Version set to "%s"', $this->version),
            LogHelper::getActionContext($context, $this)
        );
    }
}
