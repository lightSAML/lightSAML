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
use Psr\Log\LoggerInterface;

class AssertBindingTypeAction extends AbstractProfileAction
{
    /** @var string[] */
    protected $expectedBindingTypes;

    /**
     * @param LoggerInterface $logger
     * @param string[]        $expectedBindingTypes
     */
    public function __construct(LoggerInterface $logger, array $expectedBindingTypes)
    {
        parent::__construct($logger);

        $this->expectedBindingTypes = $expectedBindingTypes;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        if (false === in_array($context->getInboundContext()->getBindingType(), $this->expectedBindingTypes)) {
            $message = sprintf(
                'Unexpected binding type "%s" - expected binding types are: %s',
                $context->getInboundContext()->getBindingType(),
                implode(' ', $this->expectedBindingTypes)
            );
            $this->logger->critical($message, LogHelper::getActionErrorContext($context, $this, array(
                'actualBindingType' => $context->getInboundContext()->getBindingType(),
                'expectedBindingTypes' => $this->expectedBindingTypes,
            )));

            throw new LightSamlContextException($context, $message);
        }
    }
}
